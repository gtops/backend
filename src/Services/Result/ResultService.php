<?php

namespace App\Services\Result;
use App\Domain\Models\AgeCategory\AgeCategory;
use App\Domain\Models\Event\Event;
use App\Domain\Models\EventParticipant\EventParticipant;
use App\Domain\Models\Result\ResultOnTrialInEvent;
use App\Domain\Models\Trial;
use App\Domain\Models\User\User;
use App\Persistance\Repositories\AgeCategory\AgeCategoryRepository;
use App\Persistance\Repositories\Event\EventRepository;
use App\Persistance\Repositories\EventParticipant\EventParticipantRepository;
use App\Persistance\Repositories\LocalAdmin\LocalAdminRepository;
use App\Persistance\Repositories\Referee\RefereeInTrialOnEventRepository;
use App\Persistance\Repositories\Result\ResultRepository;
use App\Persistance\Repositories\Role\RoleRepository;
use App\Persistance\Repositories\Secretary\SecretaryOnOrganizationRepository;
use App\Persistance\Repositories\Secretary\SecretaryRepository;
use App\Persistance\Repositories\SportObject\SportObjectRepository;
use App\Persistance\Repositories\Team\TeamRepository;
use App\Persistance\Repositories\TrialRepository\TableInEventRepository;
use App\Persistance\Repositories\TrialRepository\TableRepository;
use App\Persistance\Repositories\TrialRepository\TrialInEventRepository;
use App\Persistance\Repositories\TrialRepository\TrialRepository;
use App\Persistance\Repositories\User\UserRepository;
use App\Services\Presenters\TrialsToResponsePresenter;

class ResultService
{
    private const RESULT_FOR_BRONZE = 25;
    private const RESULT_FOR_SILVER = 40;
    private const RESULT_FOR_GOLD = 60;
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
    private $resultRepository;
    private $teamRepository;
    private $ageCategoryRepository;

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
        RefereeInTrialOnEventRepository $refereeInTrialOnEventRepository,
        ResultRepository $resultRepository,
        TeamRepository $teamRepository,
        AgeCategoryRepository $ageCategoryRepository
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
        $this->resultRepository = $resultRepository;
        $this->teamRepository = $teamRepository;
        $this->ageCategoryRepository = $ageCategoryRepository;
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
        if (count($listOfAllTrials) == 0){
            return [];
        }

        $listTrialsOnEvent = $this->trialInEventRepository->getFilteredByEventId($eventId);

        $responseList = [];
        $ageCategory = $this->trialRepository->getNameOfAgeCategory($user->getAge());
        if ($event->getStatus() == Event::LEAD_UP){
            $trials = $this->getFilteredFromAllTrialsTrialsOnEvent($listOfAllTrials, $listTrialsOnEvent);
            $responseList = TrialsToResponsePresenter::getView($trials, []);
        }

        if ($event->getStatus() != Event::LEAD_UP){
            $results = $this->resultRepository->getFilteredByUserIdAndEventId($userId, $eventId);
            $trials = $this->getFilteredFromAllTrialsTrialsOnEvent($listOfAllTrials, $listTrialsOnEvent);
            $responseList = TrialsToResponsePresenter::getView($trials, $this->getArrayWithResultsForTrials($results));
        }

        $dateAboutCountOfTest = $this->getDataAboutCountOfTests($user->getGender(), $this->ageCategoryRepository->getFilteredByName($ageCategory));

        return [
            'groups' => $responseList,
            'ageCategory' => $ageCategory,
            'badge' => null,
            'countTestsForBronze' => $dateAboutCountOfTest['countTestsForBronze'] ?? null,
            'countTestForSilver' => $dateAboutCountOfTest['countTestForSilver'] ?? null,
            'countTestsForGold' => $dateAboutCountOfTest['countTestsForGold'] ?? null
        ];
    }

    public function updateResult(int $resultTrialInEventId, string $firstResult)
    {
        $badge = null;
        $result = $this->resultRepository->get($resultTrialInEventId);
        $secondResult = $this->trialRepository->getSecondResult($firstResult, $result->getResultGuideId());
        if ($secondResult >= self::RESULT_FOR_BRONZE){
            $result->setBadge('бронза');
        }

        if ($secondResult >= self::RESULT_FOR_SILVER){
            $result->setBadge('серебро');
        }

        if ($secondResult >= self::RESULT_FOR_GOLD){
            $result->setBadge('золото');
        }

        $this->resultRepository->update($result);

        /*операция по смене общего знака
        $ageCategory = $this->ageCategoryRepository->getFilteredByName($this->trialRepository->getNameOfAgeCategory($result->getUser()->getAge()));
        $listOfAllTrials = $this->trialRepository->getList($result->getUser()->getGender(), $result->getUser()->getAge());

        if (count($listOfAllTrials) == 0){
            return;
        }

        $listTrialsOnEvent = $this->trialInEventRepository->getFilteredByEventId($result->getTrialInEvent()->getEventId());
        $results = $this->resultRepository->getFilteredByUserIdAndEventId($result->get, $eventId);
        */
    }

    /**@param $trials Trial[]*/
    private function getTrialFromResultGuideWithDataToBadge($trialId, array $trials)
    {
        foreach ($trials as $trial){
            if ($trial->getTrialId() == $trialId){
                return $trial;
            }
        }

        return null;
    }

    private function getDataAboutCountOfTests(int $gender, AgeCategory $ageCategory)
    {
        if ($gender == 0){
            return [
                'countTestsForBronze' => $ageCategory->getCountTestForBronzeForWoman(),
                'countTestForSilver' => $ageCategory->getCountTestFromSilverForWoman(),
                'countTestsForGold' => $ageCategory->getCountTestsForGoldForWoman()
            ];
        }

        if ($gender == 1){
            return [
                'countTestsForBronze' => $ageCategory->getCountTestForBronzeForMan(),
                'countTestForSilver' => $ageCategory->getCountTestFromSilverForMan(),
                'countTestsForGold' => $ageCategory->getCountTestsForGoldForMan()
            ];
        }
    }

    public function getResultsForTrial(int $trialInEventId)
    {
        $trialInEvent = $this->trialInEventRepository->get($trialInEventId);
        if ($trialInEvent == null){
            return [];
        }

        $event = $this->eventRepository->get($trialInEvent->getEventId());

        $trialId = $trialInEvent->getTrial()->getTrialId();
        $eventParticipants = $this->eventParticipantRepository->getAllByEventId($event->getId());

        $eventParticipantsForTrial = [];
        $ParticipantsInTrial = $this->getParticipantsForTrial($eventParticipants, $event->getId(), $trialId);
        $results = [];

        if ($event->getStatus() == Event::LEAD_UP) {
            foreach ($ParticipantsInTrial as $participant){
                $team = $this->teamRepository->get($participant->getTeamId() ?? -1);
                if ($team == null){
                    $teamName = null;
                }else{
                    $teamName = $team->getName();
                }

                $results[] = [
                    'resultOfTrialInEventId' => null,
                    'userId' => $participant->getUser()->getId(),
                    'userName' => $participant->getUser()->getName(),
                    'teamId' => $participant->getTeamId(),
                    'teamName' => $teamName,
                    'dateOfBirth' => $participant->getUser()->getDateOfBirth(),
                    'gender' => $participant->getUser()->getGender(),
                    'firstResult' => null,
                    'secondResult' => null,
                    'badge' => null
                ];
            }
        }
        $trial = $this->trialRepository->get($trialId);
        if ($event->getStatus() != Event::LEAD_UP){
            foreach ($ParticipantsInTrial as $participant){
                $team = $this->teamRepository->get($participant->getTeamId() ?? -1);
                if ($team == null){
                    $teamName = null;
                }else{
                    $teamName = $team->getName();
                }

                $resultOfTrialOnEvent = $this->resultRepository->getFilteredByUserIdEventIdTrialId($participant->getUser()->getId(), $event->getId(), $trialId);
                $results[] = [
                    'resultOfTrialInEventId' => $resultOfTrialOnEvent->getResultTrialInEventId(),
                    'userId' => $participant->getUser()->getId(),
                    'userName' => $participant->getUser()->getName(),
                    'teamId' => $participant->getTeamId(),
                    'teamName' => $teamName,
                    'dateOfBirth' => $participant->getUser()->getDateOfBirth(),
                    'gender' => $participant->getUser()->getGender(),
                    'firstResult' => $resultOfTrialOnEvent->getFistResult(),
                    'secondResult' => $resultOfTrialOnEvent->getSecondResult(),
                    'badge' => $resultOfTrialOnEvent->getBadge()
                ];
            }
        }

        return [
            'participants' => $results,
            'trialName' => $trial->getName(),
            'isTypeTime' => $trial->isTypeTime(),
            'eventStatus' => $event->getStatus()
        ];
    }

    /**
     * @param $eventParticipants EventParticipant[]
     * @param int $eventId
     * @param int $trialId
     * @return EventParticipant[]
     */
    private function getParticipantsForTrial(array $eventParticipants, int $eventId, int $trialId)
    {
        $participants = [];
        $listTrialsOnEvent = $this->trialInEventRepository->getFilteredByEventId($eventId);
        foreach ($eventParticipants as $eventParticipant){
            $listOfAllTrials = $this->trialRepository->getList($eventParticipant->getUser()->getGender(), $eventParticipant->getUser()->getAge());
            if (count($listOfAllTrials) == 0){
                continue;
            }

            $trials = $this->getFilteredFromAllTrialsTrialsOnEvent($listOfAllTrials, $listTrialsOnEvent);
            foreach ($trials as $trial){
                if ($trial->getTrialId() == $trialId){
                    $participants[] = $eventParticipant;
                    break;
                }
            }
        }

        return $participants;
    }

    /**@param $listOfAllTrials Trial[]*/
    /**@param  $listTrialsOEvent Trial\TrialInEvent[]*/
    private function getFilteredFromAllTrialsTrialsOnEvent(array $listOfAllTrials, array $listTrialsOEvent)
    {
        $response = [];
        foreach ($listOfAllTrials as $trial){
            foreach ($listTrialsOEvent as $trialOnEvent){
                if ($trial->getTrialId() == $trialOnEvent->getTrial()->getTrialId()){
                    $response[] = $trial;
                }
            }
        }

        return $response;
    }

    /**@param  $results ResultOnTrialInEvent[]*/
    private function getArrayWithResultsForTrials(array $results)
    {
        $arrayWithResults = [];
        foreach ($results as $result){
            $arrayWithResults[$result->getTrialInEvent()->getTrial()->getTrialId()] = [
                'firstResult' => $result->getFistResult(),
                'secondResult' => $result->getSecondResult(),
                'badge' => $result->getBadge(),
                'resultTrialInEventId' => $result->getResultTrialInEventId()
            ];
        }

        return $arrayWithResults;
    }
}