<?php

namespace App\Services\TeamLead;
use App\Application\Middleware\AuthorizeMiddleware;
use App\Domain\Models\TeamLead\TeamLead;
use App\Persistance\Repositories\Role\RoleRepository;
use App\Persistance\Repositories\Team\TeamRepository;
use App\Persistance\Repositories\TeamLead\TeamLeadRepository;
use App\Persistance\Repositories\User\UserRepository;

class TeamLeadService
{
    private $teamLeadRepository;
    private $userRepository;
    private $roleRepository;
    private $teamRepository;

    public function __construct(TeamLeadRepository $teamLeadRepository, UserRepository $userRepository, RoleRepository $roleRepository, TeamRepository $teamRepository)
    {
        $this->teamLeadRepository = $teamLeadRepository;
        $this->userRepository = $userRepository;
        $this->roleRepository = $roleRepository;
        $this->teamRepository = $teamRepository;
    }

    /**@return TeamLead[]*/
    public function getAllForTeam(int $teamId):array
    {
        return $this->teamLeadRepository->getByTeamId($teamId);
    }

    public function add($email, int $teamId)
    {
        $user = $this->userRepository->getByEmail($email);
        if ($user == null){
            return -3;
        }
        $nameOfRuleOfUser = $this->roleRepository->get($user->getRoleId())->getName();
        if (!in_array($nameOfRuleOfUser, [AuthorizeMiddleware::SIMPLE_USER, AuthorizeMiddleware::TEAM_LEAD])){
            return -1;
        }

        $teamLead = $this->teamLeadRepository->getByEmailAndTeamId($email, $teamId);
        if ($teamLead != null){
            return -2;
        }

        $user->setRoleId($this->roleRepository->getByName(AuthorizeMiddleware::TEAM_LEAD)->getId());
        $this->userRepository->update($user);
        $teamLead = new TeamLead(-1, $teamId, $user->getId(), $user);
        return $this->teamLeadRepository->add($teamLead);
    }

    public function get(int $teamLeadId)
    {
        return $this->teamLeadRepository->get($teamLeadId);
    }

    public function delete(int $teamLeadId)
    {
        $teamLead = $this->teamLeadRepository->get($teamLeadId);
        $teams = $this->teamRepository->getAllForTeamLeadWithUserId($teamLead->getUserId());
        if (count($teams) == 1){
            $user = $teamLead->getUser();
            $user->setRoleId($this->roleRepository->getByName(AuthorizeMiddleware::SIMPLE_USER)->getId());
            $this->userRepository->update($user);
        }

        $this->teamLeadRepository->delete($teamLeadId);
    }
}