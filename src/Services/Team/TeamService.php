<?php

namespace App\Services\Team;
use App\Domain\Models\Team\Team;
use App\Persistance\Repositories\Team\TeamRepository;
use App\Persistance\Repositories\User\UserRepository;

class TeamService
{
    private $userRepository;
    private $teamRepository;

    public function __construct(UserRepository $userRepository, TeamRepository $teamRepository)
    {
        $this->teamRepository = $teamRepository;
        $this->userRepository = $userRepository;
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
}