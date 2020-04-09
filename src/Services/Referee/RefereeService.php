<?php

namespace App\Services\Referee;
use App\Domain\Models\Referee\RefereeOnOrganization;
use App\Domain\Models\Referee\RefereeOnTrialInEvent;
use App\Persistance\Repositories\Referee\RefereeInTrialOnEventRepository;
use App\Persistance\Repositories\Referee\RefereeRepository;
use App\Persistance\Repositories\User\UserRepository;

class RefereeService
{
    private $refereeRepository;
    private $userRepository;
    private $refereeOnTrialInEventRepository;

    public function __construct(RefereeRepository $refereeRepository, UserRepository $userRepository, RefereeInTrialOnEventRepository $refereeOnTrialInEventRepository)
    {
        $this->refereeRepository = $refereeRepository;
        $this->userRepository = $userRepository;
        $this->refereeOnTrialInEventRepository = $refereeOnTrialInEventRepository;

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

    public function addToTrialOnEvent(int $refereeInOrganizationId, int $trialInEventId)
    {
        $refereeInOrganization = $this->refereeRepository->get($refereeInOrganizationId);
        $refereeInTrialOnEvent = new RefereeOnTrialInEvent(-1, $trialInEventId, $refereeInOrganization->getUser());
        return $this->refereeOnTrialInEventRepository->add($refereeInTrialOnEvent);
    }

    public function deleteRefereeFromTrialOnEvent(int $refereeInTrialOnEventId)
    {
        $this->refereeOnTrialInEventRepository->delete($refereeInTrialOnEventId);
    }
}