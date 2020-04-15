<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 17.10.2019
 * Time: 23:43
 */

namespace App\Persistance\Repositories\Result;


use App\Domain\Models\IModel;
use App\Domain\Models\IRepository;
use App\Domain\Models\Result\ResultOnTrialInEvent;
use App\Persistance\ModelsEloquant\Result\Result as ResultPDO;
use App\Persistance\ModelsEloquant\User\User;
use App\Persistance\Repositories\TrialRepository\TrialInEventRepository;
use App\Persistance\Repositories\User\UserRepository;

class ResultRepository implements IRepository
{

    private $trialInEventRepository;
    private $userRepository;

    public function __construct(TrialInEventRepository $trialInEventRepository, UserRepository $userRepository)
    {
        $this->trialInEventRepository = $trialInEventRepository;
        $this->userRepository = $userRepository;
    }

    /**@return ResultOnTrialInEvent*/
    public function get(int $id): IModel
    {
        $results = ResultPDO::query()
            ->where('result_on_trial_in_event_id', '=', $id)
            ->get();

        if (count($results) == 0){
            return null;
        }

        return $this->getResultModels($results)[0];
    }

    private function getResultModels($results)
    {
        $resultModels = [];
        foreach ($results as $result)
        {
            $user = $this->userRepository->get($result->user_id);
            $trialInEvent = $this->trialInEventRepository->get($result->trial_in_event_id);
            $resultModels[] = new ResultOnTrialInEvent($trialInEvent, $user, $result->id_result_guide, $result->first_result, $result->second_result, $result->badge);
        }

        return $resultModels;
    }

    /**@return ResultOnTrialInEvent[]*/
    public function getFilteredByUserIdAndEventId(int $userId, int $eventId)
    {
        $results = ResultPDO::query()
            ->join('trial_in_event', 'trial_in_event.trial_in_event_id', '=', 'result_on_trial_in_event.trial_in_event_id')
            ->where('user_id', '=', $userId)
            ->where('trial_in_event.event_id', '=', $eventId)
            ->get();

        if (count($results) == 0){
            return null;
        }

        return $this->getResultModels($results);
    }
    /**
     * @inheritDoc
     */
    public function getAll(): array
    {
        // TODO: Implement getAll() method.
    }

    public function add(IModel $model):int
    {
        // TODO: Implement add() method.
    }

    public function delete(int $id)
    {
        // TODO: Implement delete() method.
    }

    public function update(IModel $model)
    {
        // TODO: Implement update() method.
    }

    /**
     * @param int $userId
     * @param $eventId
     * @param int $trialId
     * @return ResultOnTrialInEvent
     */
    public function getFilteredByUserIdEventIdTrialId(int $userId, $eventId, int $trialId)
    {
        $results = ResultPDO::query()
            ->join('trial_in_event', 'trial_in_event.trial_in_event_id', '=', 'result_on_trial_in_event.trial_in_event_id')
            ->where('user_id', '=', $userId)
            ->where('trial_in_event.event_id', '=', $eventId)
            ->where('trial_in_event.trial_id', '=', $trialId)
            ->get();

        if (count($results) == 0){
            return null;
        }

        return $this->getResultModels($results)[0];
    }
}