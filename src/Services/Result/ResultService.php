<?php

namespace App\Services\Result;
use App\Domain\Models\Event\Event;
use App\Domain\Models\Trial;
use App\Persistance\Repositories\Event\EventRepository;
use App\Persistance\Repositories\EventParticipant\EventParticipantRepository;
use App\Persistance\Repositories\LocalAdmin\LocalAdminRepository;
use App\Persistance\Repositories\Referee\RefereeInTrialOnEventRepository;
use App\Persistance\Repositories\Role\RoleRepository;
use App\Persistance\Repositories\Secretary\SecretaryOnOrganizationRepository;
use App\Persistance\Repositories\Secretary\SecretaryRepository;
use App\Persistance\Repositories\SportObject\SportObjectRepository;
use App\Persistance\Repositories\TrialRepository\TableInEventRepository;
use App\Persistance\Repositories\TrialRepository\TableRepository;
use App\Persistance\Repositories\TrialRepository\TrialInEventRepository;
use App\Persistance\Repositories\TrialRepository\TrialRepository;
use App\Persistance\Repositories\User\UserRepository;
use App\Services\Presenters\TrialsToResponsePresenter;

class ResultService
{
    private $eventRepository;
    private $localAdminRepository;
    private $roleRepository;
    private $secretaryRepository;
    private $userRepository;
    private $eventParticipantRepository;
    private $secretaryOnOrgRepository;
    private $tableInEventRepository;
    private $tableRepository;
    private $trialRepository;
    private $trialInEventRepository;
    private $sportObjectRepository;
    private $refereeInTrialOnEventRepository;

    public function __construct(
        LocalAdminRepository $localAdminRepository,
        EventRepository $eventRepository,
        SecretaryRepository $secretaryRepository,
        RoleRepository $roleRepository,
        UserRepository $userRepository,
        EventParticipantRepository $eventParticipantRepository,
        SecretaryOnOrganizationRepository $secretaryOnOrgRepository,
        TableInEventRepository $tableInEventRepository,
        TableRepository $tableRepository,
        TrialRepository $trialRepository,
        TrialInEventRepository $trialInEventRepository,
        SportObjectRepository $sportObjectRepository,
        RefereeInTrialOnEventRepository $refereeInTrialOnEventRepository
    )
    {
        $this->localAdminRepository = $localAdminRepository;
        $this->eventRepository = $eventRepository;
        $this->roleRepository = $roleRepository;
        $this->secretaryRepository = $secretaryRepository;
        $this->userRepository = $userRepository;
        $this->eventParticipantRepository = $eventParticipantRepository;
        $this->secretaryOnOrgRepository = $secretaryOnOrgRepository;
        $this->tableInEventRepository = $tableInEventRepository;
        $this->tableRepository = $tableRepository;
        $this->trialRepository = $trialRepository;
        $this->trialInEventRepository = $trialInEventRepository;
        $this->sportObjectRepository = $sportObjectRepository;
        $this->refereeInTrialOnEventRepository = $refereeInTrialOnEventRepository;
    }

    public function getResultsUfUserInEvent(int $eventId, int $userId)
    {
        $event = $this->eventRepository->get($eventId);
        if ($event == null){
            return [];
        }
        $user = $this->userRepository->get($userId);
        if ($user == null){
            return [];
        }

        $eventParticipant = $this->eventParticipantRepository->getByEmailAndEvent($user->getEmail(), $eventId);
        if ($eventParticipant == null){
            return [];
        }

        $listOfAllTrials = $this->trialRepository->getList($user->getGender(), $user->getAge());
        $listTrialsOnEvent = $this->trialInEventRepository->getFilteredByEventId($eventId);

        $responseList = [];
        $ageCategory = $this->trialRepository->getNameOfAgeCategory($user->getAge());
        if ($event->getStatus() == Event::LEAD_UP){
            $trials = $this->getFilteredFromAllTrialsTrialsOnEvent($listOfAllTrials, $listTrialsOnEvent);
            $responseList = TrialsToResponsePresenter::getView($trials, []);
        }

        if ($event->getStatus() == Event::HOLDING){

        }

        return [
            'groups' => $responseList,
            'ageCategory' => $ageCategory,
            'badge' => null
        ];
    }

    /**@var $listOfAllTrials Trial[]*/
    /**@var $listTrialsOEvent Trial\TrialInEvent[]*/
    private function getFilteredFromAllTrialsTrialsOnEvent(array $listOfAllTrials, array $listTrialsOEvent)
    {
        $response = [];
        /**@var $trial Trial*/
        foreach ($listOfAllTrials as $trial){
            /**@var $trialOnEvent Trial\TrialInEvent*/
            foreach ($listTrialsOEvent as $trialOnEvent){
                if ($trial->getTrialId() == $trialOnEvent->getTrial()->getTrialId()){
                    $response[] = $trial;
                }
            }
        }

        return $response;
    }
}