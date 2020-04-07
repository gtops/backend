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
use App\Domain\Models\Secretary\SecretaryOnOrganization;
use App\Domain\Models\User\User;
use App\Persistance\Repositories\Event\EventRepository;
use App\Persistance\Repositories\EventParticipant\EventParticipantRepository;
use App\Persistance\Repositories\LocalAdmin\LocalAdminRepository;
use App\Persistance\Repositories\Organization\OrganizationRepository;
use App\Persistance\Repositories\Role\RoleRepository;
use App\Persistance\Repositories\Secretary\SecretaryOnOrganizationRepository;
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
    private $secretaryOnOrganizationRepository;

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
        TeamLeadRepository $teamLeadRepository,
        SecretaryOnOrganizationRepository $secretaryOnOrganizationRepository
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
        $this->secretaryOnOrganizationRepository = $secretaryOnOrganizationRepository;
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
        $email = mb_strtolower($email);
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
        $user = $this->userRepository->getByEmail($emailParticipant);
        $userEmail = mb_strtolower($userEmail);
        $team = $this->teamRepository->get($teamId);
        $event = $this->eventRepository->get($team->getEventId());
        $this->addErrorIfTeamNotExists($team);
        $this->addErrorIfParticipantExistOnThisEvent($emailParticipant, $event);
        $this->addErrorIfParticipantNotExists($user);
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
        $emailParticipant = mb_strtolower($emailParticipant);
        $userEmail = mb_strtolower($userEmail);
        $event = $this->eventRepository->get($eventId);
        $this->userRepository->getByEmail($emailParticipant);
        $user = $this->userRepository->getByEmail($emailParticipant);
        $this->addErrorIfParticipantExistOnThisEvent($emailParticipant, $event);
        $this->addErrorIfUserNotExists($user);
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
        $email = mb_strtolower($email);
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

    public function hasAccessWorkWithTeamWithId(string $userRole, string $userEmail, int $teamId)
    {
        $userEmail = mb_strtolower($userEmail);
        $team = $this->teamRepository->get($teamId);
        $event = $this->eventRepository->get($team->getEventId() ?? -1);
        $organization = $this->organizationRepository->get($event->getIdOrganization() ?? -1);

        $this->addErrorIfTeamNotExists($team);

        switch ($userRole){
            case AuthorizeMiddleware::LOCAL_ADMIN:{
                return $this->localAdminHasAccessWorkWithTeam($organization, $userEmail);
            }
            case AuthorizeMiddleware::SECRETARY:{
                return $this->secretaryHasAccessWorkWithTeam($event, $userEmail);
            }
            case AuthorizeMiddleware::TEAM_LEAD:{
                return $this->teamLeadHasAccessWorkWithTeam($userEmail, $teamId);
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
        $email = mb_strtolower($email);
        if ($organization == null){
            return $this->getResponse();
        }
        $this->changeResponseStatusToFalseIfLocalAdminNotExistsInOrganization($email, $organization->getId());
        return $this->getResponse();
    }

    private function secretaryHasAccessWorkWithTeam(?IModel $event, string $email)
    {
        $email = mb_strtolower($email);
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
        $userEmail = mb_strtolower($userEmail);
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
        $email = mb_strtolower($email);
        if ($event == null){
            return $this->getResponse();
        }

        $orgId = $event->getIdOrganization();
        if(!$this->localAdminRepository->localAdminIsSetOnDB($email, $orgId)){
            $this->response = false;
        }

        return $this->getResponse();
    }

    public function hasAccessWorkWithEvent(int $eventId, int $organizationId, string $userEmail, $role)
    {
        $userEmail = mb_strtolower($userEmail);
        $user = $this->userRepository->getByEmail($userEmail);
        $event = $this->eventRepository->get($eventId);
        $organization = $this->organizationRepository->get($organizationId);
        $this->addErrorIfEventNoInLeadUpStatus($event);
        $this->addErrorIfEventNotExists($event);
        $this->addErrorIfOrganizationNotExist($organization);
        $this->addErrorIfEventNotExistOnOrganization($event, $organization);

        switch ($role){
            case AuthorizeMiddleware::LOCAL_ADMIN:{
                return $this->localAdminHasAccessWorkWithOrganization($userEmail, $organizationId);
            }
            case AuthorizeMiddleware::SECRETARY:{
                return $this->secretaryHasAccessWorkWithEvent($userEmail, $eventId);
            }
        }
        $this->response = false;
        return $this->getResponse();
    }

    public function hasAccessAddSecretaryToOrganization(string $userRole, string $localAdminEmail, int $organizationId, $secretaryEmail)
    {
        $organization = $this->organizationRepository->get($organizationId);
        $this->addErrorIfOrganizationNotExist($organization);
        $secretaryOnOrganization = $this->secretaryOnOrganizationRepository->getByEmailAndOrgId($secretaryEmail, $organizationId);
        $this->addErrorIfSecretaryFoundOnOrganization($secretaryOnOrganization);
        $user = $this->userRepository->getByEmail($secretaryEmail);
        $this->addErrorIfUserNotExists($user);
        $this->addErrorIfRoleOfUserNotEqual($user, [AuthorizeMiddleware::SIMPLE_USER, AuthorizeMiddleware::SECRETARY]);
        if ($userRole == AuthorizeMiddleware::LOCAL_ADMIN){
            return $this->localAdminHasAccessWorkWithOrganization($localAdminEmail, $organizationId);
        }

        return false;
    }

    public function hasAccessDeleteSecretaryFromOrganization(string $userRole, string $localAdminEmail, int $organizationId, int $secretaryId)
    {
        $organization = $this->organizationRepository->get($organizationId);
        $this->addErrorIfOrganizationNotExist($organization);
        $secretary = $this->secretaryOnOrganizationRepository->get($secretaryId);
        $this->addErrorIfSecretaryNotFoundOnOrganization($secretary, $organizationId);
        if ($userRole == AuthorizeMiddleware::LOCAL_ADMIN){
            return $this->localAdminHasAccessWorkWithOrganization($localAdminEmail, $organizationId);
        }

        return false;
    }

    private function addErrorIfRoleOfUserNotEqual(?User $user, array $roles){
        if ($user == null){
            return;
        }

        $nameOfRole = $this->rolRepository->get($user->getRoleId())->getName();
        if (!(in_array($nameOfRole, $roles))){
            $this->addError(new ActionError(ActionError::BAD_REQUEST, 'Этот пользователь уже имеет другую роль'));
        }
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

    private function localAdminHasAccessWorkWithOrganization(string $userEmail, int $organizationId)
    {
        $userEmail = mb_strtolower($userEmail);
        $organizationIdOfLocalAdmin = $this->localAdminRepository->getOrganizationIdFilteredByEmail($userEmail);
        if ($organizationId != $organizationIdOfLocalAdmin){
            $this->response = false;
        }

        return $this->getResponse();
    }

    private function secretaryHasAccessWorkWithEvent(string $userEmail, int $eventId)
    {
        $userEmail = mb_strtolower($userEmail);
        $secretaries = $this->secretaryRepository->getFilteredByEventId($eventId);
        foreach ($secretaries as $secretary){
            if ($secretary->getUser()->getEmail() == $userEmail){
                return $this->getResponse();
            }
        }

        $this->response = false;

        return $this->getResponse();
    }

    private function addErrorIfUserNotExists(?User $user)
    {
        if ($user == null){
            $this->addError(new ActionError(ActionError::BAD_REQUEST, 'Такого пользователя не существует'));
        }
    }

    private function teamLeadHasAccessWorkWithTeam(string $userEmail, int $teamId)
    {
        if($this->teamLeadRepository->getByEmailAndTeamId($userEmail, $teamId) == null){
            $this->response = false;
        }

        return $this->getResponse();
    }

    private function addErrorIfSecretaryFoundOnOrganization(?SecretaryOnOrganization $secretaryOnOrganization)
    {
        if ($secretaryOnOrganization !== null){
            $this->addError(new ActionError(ActionError::BAD_REQUEST, 'Такой секретарь уже есть в справочнике этой организации'));
        }
    }


    /**@var $secretary SecretaryOnOrganization*/
    private function addErrorIfSecretaryNotFoundOnOrganization(?IModel $secretary, int $organizationId)
    {
        if ($secretary == null){
            return;
        }

        if ($secretary->getOrganizationId() !== $organizationId){
            $this->addError(new ActionError(ActionError::BAD_REQUEST, 'Данный секретарь не относится к этой организации'));
        }
    }
}