<?php


namespace App\Domain\Models\Trial;


use App\Domain\Models\IModel;
use App\Domain\Models\Referee\RefereeOnTrialInEvent;
use App\Domain\Models\SportObject\SportObject;

class TrialInEvent implements IModel
{
    private $trialInEventId;
    private $trial;
    private $eventId;
    private $sportObject;
    private $referies;

    public function __construct(int $trialInEventId, Trial $trial, int $eventId, SportObject $sportObject)
    {
        $this->trialInEventId = $trialInEventId;
        $this->trial = $trial;
        $this->eventId = $eventId;
        $this->sportObject = $sportObject;
    }

    public function addReferee(RefereeOnTrialInEvent $referee){
        $this->referies[] = $referee;
    }

    /**
     * @param RefereeOnTrialInEvent $referies
     */
    public function setReferies(array $referies)
    {
        $this->referies = $referies;
    }
    /**
     * @return int
     */
    public function getEventId(): int
    {
        return $this->eventId;
    }

    /**
     * @return mixed
     */
    public function getReferies()
    {
        return $this->referies;
    }

    /**
     * @return SportObject
     */
    public function getSportObject(): SportObject
    {
        return $this->sportObject;
    }

    /**
     * @return Trial
     */
    public function getTrial(): Trial
    {
        return $this->trial;
    }

    /**
     * @return int
     */
    public function getTrialInEventId(): int
    {
        return $this->trialInEventId;
    }

    public function toArray(): array
    {
        $referies = $this->getReferies();
        $referies = [];

        foreach ($referies as $item){
            $referies[] = $item->toArray();
        }

        return [
            'trialInEventId' => $this->getTrialInEventId(),
            'trialId' => $this->getTrial()->getTrialId(),
            'trialName' => $this->getTrial()->getName(),
            'trialIsTypeTime' => $this->getTrial()->isTypeTime(),
            'tableId' => $this->getTrial()->getTableId(),
            'eventId' => $this->getEventId(),
            'sportObjectId' => $this->getSportObject()->getSportObjectId(),
            'sportObjectName' => $this->getSportObject()->getName(),
            'sportObjectAddress' => $this->getSportObject()->getAddress(),
            'sportObjectDescription' => $this->getSportObject()->getDescription(),
            'referies' => $referies
        ];
    }
}