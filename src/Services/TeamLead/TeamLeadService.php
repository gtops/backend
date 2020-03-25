<?php

namespace App\Services\TeamLead;
use App\Domain\Models\TeamLead\TeamLead;
use App\Persistance\Repositories\TeamLead\TeamLeadRepository;

class TeamLeadService
{
    private $teamLeadRepository;

    public function __construct(TeamLeadRepository $teamLeadRepository)
    {
        $this->teamLeadRepository = $teamLeadRepository;
    }

    /**@return TeamLead[]*/
    public function getAllForTeam(int $teamId):array
    {
        return $this->teamLeadRepository->getByTeamId($teamId);
    }
}