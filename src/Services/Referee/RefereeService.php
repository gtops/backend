<?php

namespace App\Services\Referee;
use App\Domain\Models\Referee\RefereeOnOrganization;
use App\Persistance\Repositories\Referee\RefereeRepository;
use App\Persistance\Repositories\User\UserRepository;

class RefereeService
{
    private $refereeRepository;
    private $userRepository;
    public function __construct(RefereeRepository $refereeRepository, UserRepository $userRepository)
    {
        $this->refereeRepository = $refereeRepository;
        $this->userRepository = $userRepository;
    }

    public function addToOrganization(int $organizationId, $refereeEmail)
    {
        $user = $this->userRepository->getByEmail($refereeEmail);
        $refereeOnOrganization = new RefereeOnOrganization(-1, $organizationId, $user->getId(), $user);
        return $this->refereeRepository->add($refereeOnOrganization);
    }

    public function get(int $organizationId)
    {
        return $this->refereeRepository->getFilteredByOrgId($organizationId);
    }

    public function delete(int $refereeId)
    {
        $this->refereeRepository->delete($refereeId);
    }
}