<?php

namespace App\Services\Team;
use App\Application\Middleware\AuthorizeMiddleware;
use App\Domain\Models\Team\Team;
use App\Persistance\Repositories\Event\EventRepository;
use App\Persistance\Repositories\LocalAdmin\LocalAdminRepository;
use App\Persistance\Repositories\Team\TeamRepository;
use App\Persistance\Repositories\TeamLead\TeamLeadRepository;
use App\Persistance\Repositories\User\UserRepository;

class TeamService
{
    private $userRepository;
    private $teamRepository;
    private $teamLeadRepisotory;
    private $eventRepository;
    private $localAdminRepository;
    public function __construct(UserRepository $userRepository, TeamRepository $teamRepository, EventRepository $eventRepository, LocalAdminRepository $localAdminRepository)
    {
        $this->teamRepository = $teamRepository;
        $this->userRepository = $userRepository;
        $this->eventRepository = $eventRepository;
        $this->localAdminRepository = $localAdminRepository;
    }

    public function add($name, int $eventId)
    {
        $team = new Team(-1, $eventId, $name);
        return $this->teamRepository->add($team);
    }

    /**
     * @param int $eventId
     * @param int $organizationId
     * @return Team[]|array
     */
    public function getAll(int $eventId, int $organizationId)
    {
        return $this->teamRepository->getAllFilteredByEventIdOrgId($eventId, $organizationId);
    }

    /**@return Team[]*/
    public function getListForUser(string $email, string $role):array
    {
        $user = $this->userRepository->getByEmail($email);
        $teams = [];
        switch ($role){
            case AuthorizeMiddleware::TEAM_LEAD:{
                $teams = $this->teamRepository->getAllForTeamLeadWithUserId($user->getId());
                break;
            }
            case AuthorizeMiddleware::SECRETARY:{
                $teams = $this->teamRepository->getAllForSecretaryWithUserId($user->getId());
                break;
            }
            case AuthorizeMiddleware::LOCAL_ADMIN:{
                $organizationId = $this->localAdminRepository->getOrganizationIdFilteredByEmail($user->getEmail());
                $teams = $this->teamRepository->getAllForOrganizationId($organizationId);
                break;
            }
        }

        $response = [];
        foreach ($teams as $team){
            $teamArray = $team->toArray();
            $event = $this->eventRepository->get($team->getEventId());
            $teamArray['organizationId'] = $event->getIdOrganization();
            $teamArray['nameOfEvent'] = $event->getName();
            $response[] = $teamArray;
        }

        return $response;
    }
}