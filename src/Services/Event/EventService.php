<?php

namespace App\Services\Event;
use App\Application\Actions\ActionError;
use App\Application\Middleware\AuthorizeMiddleware;
use App\Domain\Models\EventParticipant\EventParticipant;
use App\Domain\Models\Secretary\Secretary;
use App\Persistance\Repositories\Event\EventRepository;
use App\Persistance\Repositories\EventParticipant\EventParticipantRepository;
use App\Persistance\Repositories\LocalAdmin\LocalAdminRepository;
use App\Domain\Models\Event\Event;
use App\Persistance\Repositories\Role\RoleRepository;
use App\Persistance\Repositories\Secretary\SecretaryOnOrganizationRepository;
use App\Persistance\Repositories\Secretary\SecretaryRepository;
use App\Persistance\Repositories\TrialRepository\TableInEventRepository;
use App\Persistance\Repositories\User\UserRepository;
use Psr\Http\Message\ResponseInterface;

class EventService
{
    private $eventRepository;
    private $localAdminRepository;
    private $roleRepository;
    private $secretaryRepository;
    private $userRepository;
    private $eventParticipantRepository;
    private $secretaryOnOrgRepository;
    private $tableInEventRepository;

    public function __construct(LocalAdminRepository $localAdminRepository, EventRepository $eventRepository, SecretaryRepository $secretaryRepository, RoleRepository $roleRepository, UserRepository $userRepository, EventParticipantRepository $eventParticipantRepository, SecretaryOnOrganizationRepository $secretaryOnOrgRepository, TableInEventRepository $tableInEventRepository)
    {
        $this->localAdminRepository = $localAdminRepository;
        $this->eventRepository = $eventRepository;
        $this->roleRepository = $roleRepository;
        $this->secretaryRepository = $secretaryRepository;
        $this->userRepository = $userRepository;
        $this->eventParticipantRepository = $eventParticipantRepository;
        $this->secretaryOnOrgRepository = $secretaryOnOrgRepository;
        $this->tableInEventRepository = $tableInEventRepository;
    }

    public function add(Event $event, string $userEmail, ResponseInterface $response)
    {
        if (!$this->localAdminRepository->localAdminIsSetOnDB($userEmail, $event->getIdOrganization())){
            return $response->withStatus(403);
        }

        return $this->eventRepository->add($event);
    }

    public function delete(int $organizationId, int $eventId, ResponseInterface $response, string $userEmail)
    {
        $response = $this->getInitedResponseWithStatusIfErrorOfAccess($response, $userEmail, $organizationId, $eventId);
        if ($response->getStatusCode() != 200){
            return $response;
        }

        $roleIdOfSimpleUser = $this->roleRepository->getByName(AuthorizeMiddleware::SIMPLE_USER)->getId();
        $secretariesInOrganization = $this->secretaryRepository->getFilteredByEventId($eventId);

        $this->changeAdminStatusToSimple($secretariesInOrganization, $roleIdOfSimpleUser);

        $this->eventRepository->delete($eventId);
    }

    /**@var $admins Secretary[]*/
    private function changeAdminStatusToSimple(?array $admins, $simpleRoleId)
    {
        if ($admins == null){
            return;
        }

        foreach ($admins as $admin){
            $user = $admin->getUser();
            $secretaryInEvents = $this->secretaryRepository->getFilteredByUserEmail($user->getEmail());

            if (count($secretaryInEvents) == 1) {
                $user->setRoleId($simpleRoleId);
                $this->userRepository->update($user);
            }
        }
    }

    public function get(int $organizationId, int $eventId, ResponseInterface $response)
    {
        /**@var $event Event*/
        $event = $this->eventRepository->get($eventId);
        if ($event->getIdOrganization() != $organizationId){
            return $response->withStatus(400);
        }

        return $this->eventRepository->get($eventId);
    }

    private function getInitedResponseWithStatusIfErrorOfAccess(ResponseInterface $response, string $userEmail, int $organizationId, int $eventId)
    {
        if (!$this->localAdminRepository->localAdminIsSetOnDB($userEmail, $organizationId)){
            return $response->withStatus(403);
        }

        /**@var $event Event*/
        $event = $this->eventRepository->get($eventId);
        if ($event->getIdOrganization() != $organizationId){
            return $response->withStatus(403);
        }

        return $response->withStatus(200);
    }

    public function getAll(int $organizationId)
    {
        return $this->eventRepository->getAllFilteredByOrganizationId($organizationId);
    }

    public function update(Event $event, string $userEmail, ResponseInterface $response)
    {
        $this->eventRepository->update($event);

        return $response;
    }

    public function applyToEvent(int $eventId, string $userEmail, bool $confirmed, $teamId = null)
    {
        $user = $this->userRepository->getByEmail($userEmail);
        $participant = new EventParticipant(-1, $eventId, $user->getId(), $confirmed, $user, $teamId);
        return $this->eventParticipantRepository->add($participant);
    }

    public function getForSecretary(string $userEmail)
    {
        $secretaries = $this->secretaryRepository->getFilteredByUserEmail($userEmail);
        $events = [];

        foreach ($secretaries as $secretary){
            $secretaryInOrg = $this->secretaryOnOrgRepository->getByEmailAndOrgId($secretary->getUser()->getEmail(), $secretary->getOrganizationId());
            if ($secretaryInOrg != null) {
                $events[] = $this->eventRepository->get($secretary->getEventId());
            }
        }

        return $events;
    }

    /**@return Event[]*/
    public function getForUser(string $userEmail):array
    {
        $participants = $this->eventParticipantRepository->getByEmail($userEmail);
        $events = [];
        foreach ($participants as $participant){
            $event = $this->eventRepository->get($participant->getEventId())->toArray();
            $event['userConfirmed'] = $participant->isConfirmed();
            $events[] = $event;
        }

        return $events;
    }

    public function getTable(int $eventId)
    {
        return $this->tableInEventRepository->getFilteredByEventId($eventId);
    }
}