<?php

namespace App\Services\AccessService;
use App\Application\Actions\ActionError;
use App\Application\Middleware\AuthorizeMiddleware;
use App\Domain\Models\Event\Event;
use App\Domain\Models\EventParticipant\EventParticipant;
use App\Domain\Models\IModel;
use App\Domain\Models\LocalAdmin\LocalAdmin;
use App\Domain\Models\Organization;
use App\Domain\Models\Secretary\Secretary;
use App\Persistance\Repositories\Event\EventRepository;
use App\Persistance\Repositories\EventParticipant\EventParticipantRepository;
use App\Persistance\Repositories\LocalAdmin\LocalAdminRepository;
use App\Persistance\Repositories\Organization\OrganizationRepository;
use App\Persistance\Repositories\Role\RoleRepository;
use App\Persistance\Repositories\Secretary\SecretaryRepository;
use App\Persistance\Repositories\User\UserRepository;
use Illuminate\Database\Eloquent\Model;

class AccessService
{
    private $userRepository;
    private $localAdminRepository;
    private $secretaryRepository;
    private $organizationRepository;
    private $rolRepository;
    private $eventRepository;
    private $errors;
    private $response;
    private $eventParticipantRepository;

    public function __construct
    (
        UserRepository $userRepository,
        LocalAdminRepository $localAdminRepository,
        SecretaryRepository $secretaryRepository,
        OrganizationRepository $organizationRepository,
        RoleRepository $roleRepository,
        EventRepository $eventRepository,
        EventParticipantRepository $eventParticipantRepository
    )
    {
        $this->userRepository = $userRepository;
        $this->localAdminRepository = $localAdminRepository;
        $this->secretaryRepository = $secretaryRepository;
        $this->organizationRepository = $organizationRepository;
        $this->rolRepository = $roleRepository;
        $this->eventRepository = $eventRepository;
        $this->eventParticipantRepository = $eventParticipantRepository;
        $this->errors = [];
        $this->response = true;
    }

    private function addError(ActionError $actionError)
    {
        $this->errors[] = $actionError;
    }

    private function getErrorsInJson():array
    {
        $errorsInJson = [];
        foreach ($this->errors as $error){
            $errorsInJson[] = $error->jsonSerialize();
        }

        return$errorsInJson;
    }

    public function hasAccessApplyToEvent(int $eventId, string $email)
    {
        $user = $this->userRepository->getByEmail($email);
        $event = $this->eventRepository->get($eventId);
        $participant = $this->eventParticipantRepository->getByEmail($email, $eventId);
        $this->addErrorIfEventNoInLeadUpStatus($event);
        $this->addErrorIfEventNotExists($event);
        $this->addErrorIfParticipantExistOnEvent($participant);
        return $this->getResponse();
    }

    private function addErrorIfParticipantExistOnEvent(?EventParticipant $eventParticipant)
    {
        if ($eventParticipant != null){
            $this->addError(new ActionError(ActionError::BAD_REQUEST, 'вы уже подавали заявку на участие'));
        }
    }

    public function hasAccessWorkWithTeam(string $role, int $organizationId, int $eventId, string $email)
    {
        $event = $this->eventRepository->get($eventId);
        $organization = $this->organizationRepository->get($organizationId);

        $this->addErrorIfEventNotExists($event);
        $this->addErrorIfOrganizationNotExist($organization);
        $this->addErrorIfEventNotExistOnOrganization($event, $organization);

        switch ($role){
            case AuthorizeMiddleware::LOCAL_ADMIN:{
                return $this->localAdminHasAccessWorkWithTeam($organization, $email);
            }
            case AuthorizeMiddleware::SECRETARY:{
                return $this->secretaryHasAccessWorkWithTeam($event, $email);
            }
            default:{
                return false;
            }
        }
    }

    private function getResponse()
    {
        if (count($this->errors) == 0){
            return $this->response;
        }

        return $this->getErrorsInJson();
    }

    /**
     * @param LocalAdmin $localAdmin
     * @param Organization $organization
     * @param string $email
     * @return array|bool
     */
    private function localAdminHasAccessWorkWithTeam(?IModel $organization, string $email)
    {
        if ($organization == null){
            return $this->getResponse();
        }
        $this->changeResponseStatusToFalseIfLocalAdminNotExistsInOrganization($email, $organization->getId());
        return $this->getResponse();
    }

    private function secretaryHasAccessWorkWithTeam(?IModel $event, string $email)
    {
        if ($event == null){
            return $this->getResponse();
        }
        
        $secretaries = $this->secretaryRepository->getFilteredByUserEmail($email);
        $this->changeResponseStatusToFalseIfSecretaryNotExistInEvent($secretaries, $event);
        return $this->getResponse();
    }

    private function addErrorIfOrganizationNotExist(?IModel $organization)
    {
        if ($organization == null){
            $this->addError(new ActionError(ActionError::BAD_REQUEST, 'такой организации не существует'));
        }
    }

    private function addErrorIfEventNotExists(?IModel $event)
    {
        /**@var $event Event*/
        if ($event == null){
            $this->addError(new ActionError(ActionError::BAD_REQUEST, 'такого мероприятия не существует'));
        }
    }

    /**@var $event Event*/
    private function addErrorIfEventNoInLeadUpStatus(?IModel $event)
    {
        if ($event == null){
            return;
        }
        
        if ($event->getStatus() != Event::LEAD_UP){
            $this->addError(new ActionError(ActionError::BAD_REQUEST, 'данное действие возможно только при статусе мероприятия `'.Event::LEAD_UP.'`'));
        }
    }

    /**
     * @param Event|null $event
     * @param Organization|null $organization
     */
    private function addErrorIfEventNotExistOnOrganization(?IModel $event, ?IModel $organization)
    {
        if ($event == null || $organization == null){
            return;
        }

        if ($event->getIdOrganization() != $organization->getId()){
            $this->addError(new ActionError(ActionError::BAD_REQUEST, 'такого мероприятия не существует в рамках данной организации'));
        }
    }

    private function changeResponseStatusToFalseIfLocalAdminNotExistsInOrganization(string $localAdminEmail, int $organizationId)
    {
        if(!$this->localAdminRepository->localAdminIsSetOnDB($localAdminEmail, $organizationId)) {
            $this->response = false;
        }
    }

    public function hasAccessWorkWithParticipant(string $userEmail, int $eventId, int $participantId, string $userRole)
    {
        $event = $this->eventRepository->get($eventId);
        $participant = $this->eventParticipantRepository->get($participantId);
        $this->addErrorIfEventNoInLeadUpStatus($event);
        $this->addErrorIfEventNotExists($event);
        $this->addErrorIfOnEventNotExistThisParticipant($event, $participant);

        switch ($userRole){
            case AuthorizeMiddleware::LOCAL_ADMIN:{
                return $this->localAdminHasAccessWorkWithParticipant($userEmail, $event);
            }
            case AuthorizeMiddleware::SECRETARY:{
                return true;
            }
            default:{
                return false;
            }
        }
    }

    /**@var $event Event*/
    private function localAdminHasAccessWorkWithParticipant(string $email, ?IModel $event){
        if ($event == null){
            return $this->getResponse();
        }

        $orgId = $event->getIdOrganization();
        if(!$this->localAdminRepository->localAdminIsSetOnDB($email, $orgId)){
            $this->response = false;
        }

        return $this->getResponse();
    }

    private function changeResponseStatusToFalseIfSecretaryNotExistInOrganization(?Secretary $secretary, ?Organization $organization)
    {
        if ($organization == null){
            return;
        }

        if ($secretary == null){
            $this->response = false;
            return;
        }

        if ($organization->getId() != $secretary->getOrganizationId()){
            $this->response = false;
        }
    }

    /**
     * @param Secretary[]|null $secretaries
     * @param Event|null $event
     */
    private function changeResponseStatusToFalseIfSecretaryNotExistInEvent(?array $secretaries, ?IModel $event)
    {
        if ($secretaries == null){
            $this->response = false;
            return;
        }

        if ($event == null){
            return;
        }

        foreach ($secretaries as $secretary){
            if ($secretary->getEventId() == $event->getId()){
                return;
            }
        }

        $this->response = false;
    }

    /**@var $participant EventParticipant*/
    private function addErrorIfOnEventNotExistThisParticipant(?IModel $event, ?IModel $participant)
    {
        if ($event == null || $participant == null){
            return;
        }

        if ($event->getId() !== $participant->getEventId()){
            $this->addError(new ActionError(ActionError::BAD_REQUEST, 'переданный участник не относится к данному мероприятию'));
        }
    }
}