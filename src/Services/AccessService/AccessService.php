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
use App\Domain\Models\User\User;
use App\Persistance\Repositories\Event\EventRepository;
use App\Persistance\Repositories\EventParticipant\EventParticipantRepository;
use App\Persistance\Repositories\LocalAdmin\LocalAdminRepository;
use App\Persistance\Repositories\Organization\OrganizationRepository;
use App\Persistance\Repositories\Role\RoleRepository;
use App\Persistance\Repositories\Secretary\SecretaryRepository;
use App\Persistance\Repositories\Team\TeamRepository;
use App\Persistance\Repositories\TeamLead\TeamLeadRepository;
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
    private $teamRepository;
    private $teamLeadRepository;
    public function __construct
    (
        UserRepository $userRepository,
        LocalAdminRepository $localAdminRepository,
        SecretaryRepository $secretaryRepository,
        OrganizationRepository $organizationRepository,
        RoleRepository $roleRepository,
        EventRepository $eventRepository,
        EventParticipantRepository $eventParticipantRepository,
        TeamRepository $teamRepository,
        TeamLeadRepository $teamLeadRepository
    )
    {
        $this->userRepository = $userRepository;
        $this->localAdminRepository = $localAdminRepository;
        $this->secretaryRepository = $secretaryRepository;
        $this->organizationRepository = $organizationRepository;
        $this->rolRepository = $roleRepository;
        $this->eventRepository = $eventRepository;
        $this->eventParticipantRepository = $eventParticipantRepository;
        $this->teamRepository = $teamRepository;
        $this->teamLeadRepository = $teamLeadRepository;
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
        $participant = $this->eventParticipantRepository->getByEmailAndEvent($email, $eventId);
        $this->addErrorIfEventNoInLeadUpStatus($event);
        $this->addErrorIfEventNotExists($event);
        $this->addErrorIfParticipantExistOnEvent($participant);
        return $this->getResponse();
    }

    private function addErrorIfParticipantExistOnEvent(?EventParticipant $eventParticipant)
    {
        if ($eventParticipant != null){
            $this->addError(new ActionError(ActionError::BAD_REQUEST, 'Вы уже подавали заявку на участие'));
        }
    }

    public function hasAccessAddParticipantToTeam(string $userEmail, string $userRole, int $teamId, string $emailParticipant)
    {
        $team = $this->teamRepository->get($teamId);
        $event = $this->eventRepository->get($team->getEventId());
        $this->addErrorIfTeamNotExists($team);
        $this->addErrorIfParticipantExistOnThisEvent($emailParticipant, $event);

        switch ($userRole){
            case AuthorizeMiddleware::LOCAL_ADMIN:{
                return $this->localAdminHasAccessWorkWithParticipant($userEmail, $event);
            }
            case AuthorizeMiddleware::SECRETARY:{
                return $this->secretaryHasAccessWorkWithParticipant($userEmail, $event);
            }
            case AuthorizeMiddleware::TEAM_LEAD:{
                return $this->teamLeadHasAccessWorkWithParticipantOnTeam($teamId, $userEmail, $event);
            }
        }

        return false;
    }


    public function hasAccessAddParticipantToEvent(string $userEmail, string $userRole, int $eventId, $emailParticipant)
    {
        $event = $this->eventRepository->get($eventId);
        $this->addErrorIfParticipantExistOnThisEvent($emailParticipant, $event);
        switch ($userRole){
            case AuthorizeMiddleware::LOCAL_ADMIN:{
                return $this->localAdminHasAccessWorkWithParticipant($userEmail, $event);
            }
            case AuthorizeMiddleware::SECRETARY:{
                return $this->secretaryHasAccessWorkWithParticipant($userEmail, $event);
            }
        }

        return false;
    }

    private function teamLeadHasAccessWorkWithParticipantOnTeam(int $teamId, string $email, ?IModel $event)
    {
        $teamLead = $this->teamLeadRepository->getByEmailAndTeamId($email, $teamId);
        if ($teamLead == null){
            $this->response = false;
        }

        if ($event->getStatus() != Event::LEAD_UP){
            $this->addError(new ActionError(ActionError::BAD_REQUEST, 'Данное действие возможно только при статусе мероприятия `'.Event::LEAD_UP.'`'));
        }

        return $this->getResponse();
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
        }

        return false;
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
            $this->addError(new ActionError(ActionError::BAD_REQUEST, 'Такой организации не существует'));
        }
    }

    private function addErrorIfEventNotExists(?IModel $event)
    {
        /**@var $event Event*/
        if ($event == null){
            $this->addError(new ActionError(ActionError::BAD_REQUEST, 'Такого мероприятия не существует'));
        }
    }

    /**@var $event Event*/
    private function addErrorIfEventNoInLeadUpStatus(?IModel $event)
    {
        if ($event == null){
            return;
        }
        
        if ($event->getStatus() != Event::LEAD_UP){
            $this->addError(new ActionError(ActionError::BAD_REQUEST, 'Данное действие возможно только при статусе мероприятия `'.Event::LEAD_UP.'`'));
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
            $this->addError(new ActionError(ActionError::BAD_REQUEST, 'Такого мероприятия не существует в рамках данной организации'));
        }
    }

    private function changeResponseStatusToFalseIfLocalAdminNotExistsInOrganization(string $localAdminEmail, int $organizationId)
    {
        if(!$this->localAdminRepository->localAdminIsSetOnDB($localAdminEmail, $organizationId)) {
            $this->response = false;
        }
    }

    public function hasAccessWorkWithParticipant(string $userEmail, int $participantId, string $userRole)
    {
        $participant = $this->eventParticipantRepository->get($participantId);
        $this->addErrorIfParticipantNotExists($participant);
        $event = $this->eventRepository->get($participant->getEventId() ?? -1);
        switch ($userRole){
            case AuthorizeMiddleware::LOCAL_ADMIN:{
                return $this->localAdminHasAccessWorkWithParticipant($userEmail, $event);
            }
            case AuthorizeMiddleware::SECRETARY:{
                return $this->secretaryHasAccessWorkWithParticipant($userEmail, $event);
            }
            case AuthorizeMiddleware::TEAM_LEAD:{
                return $this->teamLeadHasAccessWorkWithParticipantOnTeam($participant->getTeamId(), $userEmail, $event);
            }
        }

        return false;
    }

    /**@var $event Event*/
    private function secretaryHasAccessWorkWithParticipant(string $userEmail, ?IModel $event)
    {
        $userEmail = mb_strtolower($userEmail);
        if ($event == null){
            return $this->getResponse();
        }

        if ($event->getStatus() != Event::LEAD_UP){
            $this->response = false;
        }

        $secretaries = $this->secretaryRepository->getFilteredByEventId($event->getId());

        foreach ($secretaries as $secretary){
            if ($secretary->getUser()->getEmail() == $userEmail){
                return $this->getResponse();
            }
        }

        $this->response = false;
        return $this->getResponse();
    }

    private function addErrorIfParticipantNotExists(?IModel $participant)
    {
        if ($participant == null){
            $this->addError(new ActionError(ActionError::BAD_REQUEST, 'Переданный участник не существует'));
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
            $this->addError(new ActionError(ActionError::BAD_REQUEST, 'Переданный участник не относится к данному мероприятию'));
        }
    }

    private function addErrorIfParticipantExistOnThisEvent(string $emailParticipant, ?IModel $event)
    {
        if ($event == null){
            return;
        }

        if ($this->eventParticipantRepository->getByEmailAndEvent($emailParticipant, $event->getId()) != null){
            $this->addError(new ActionError(ActionError::BAD_REQUEST, 'Такой участник уже есть в мероприятии'));
        }
    }

    private function addErrorIfTeamNotExists(?IModel $team)
    {
        if ($team == null){
            $this->addError(new ActionError(ActionError::BAD_REQUEST, 'Такой команды не существует'));
        }
    }
}