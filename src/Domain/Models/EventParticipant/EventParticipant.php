<?php

namespace App\Domain\Models\EventParticipant;

use App\Domain\Models\IModel;

class EventParticipant implements IModel
{
    private $eventParticipantId;
    private $eventId;
    private $userId;
    private $teamId;
    private $confirmed;

    public function __construct(int $eventParticipantId, int $eventId,  int $userId, bool $confirmed, ?int $teamId = null)
    {
        $this->eventParticipantId = $eventParticipantId;
        $this->eventId = $eventId;
        $this->userId = $userId;
        $this->teamId = $teamId;
        $this->confirmed = $confirmed;
    }

    /**
     * @return int
     */
    public function getEventId(): int
    {
        return $this->eventId;
    }

    /**
     * @return int
     */
    public function getEventParticipantId(): int
    {
        return $this->eventParticipantId;
    }

    /**
     * @return int
     */
    public function getTeamId(): ?int
    {
        return $this->teamId;
    }

    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->userId;
    }

    /**
     * @return bool
     */
    public function isConfirmed(): bool
    {
        return $this->confirmed;
    }

    public function toArray(): array
    {
        return [
            'EventParticipantId' => $this->getEventParticipantId(),
            'userId' => $this->getUserId(),
            'eventId' => $this->getEventId(),
            'teamId' => $this->getTeamId(),
            'isConfirmed' => $this->isConfirmed()
        ];
    }
}