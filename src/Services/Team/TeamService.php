<?php

namespace App\Services\Team;
use App\Persistance\Repositories\Role\TeamRepository;
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
}