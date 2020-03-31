<?php

namespace App\Services\TeamLead;
use App\Application\Middleware\AuthorizeMiddleware;
use App\Domain\Models\TeamLead\TeamLead;
use App\Persistance\Repositories\Role\RoleRepository;
use App\Persistance\Repositories\TeamLead\TeamLeadRepository;
use App\Persistance\Repositories\User\UserRepository;

class TeamLeadService
{
    private $teamLeadRepository;
    private $userRepository;
    private $roleRepository;

    public function __construct(TeamLeadRepository $teamLeadRepository, UserRepository $userRepository, RoleRepository $roleRepository)
    {
        $this->teamLeadRepository = $teamLeadRepository;
        $this->userRepository = $userRepository;
        $this->roleRepository = $roleRepository;
    }

    /**@return TeamLead[]*/
    public function getAllForTeam(int $teamId):array
    {
        return $this->teamLeadRepository->getByTeamId($teamId);
    }

    public function add($email, int $teamId)
    {
        $user = $this->userRepository->getByEmail($email);

        if ($user->getId() != $this->roleRepository->getByName(AuthorizeMiddleware::SIMPLE_USER)->getId()){
            return -1;
        }

        $user->setRoleId($this->roleRepository->getByName(AuthorizeMiddleware::TEAM_LEAD)->getId());
        $this->userRepository->update($user);
        $teamLead = new TeamLead(-1, $teamId, $user->getId(), $user);
        return $this->teamLeadRepository->add($teamLead);
    }
}