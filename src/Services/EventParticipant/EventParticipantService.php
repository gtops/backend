<?php

namespace App\Services\EventParticipant;
use App\Domain\Models\EventParticipant\EventParticipant;
use App\Persistance\Repositories\EventParticipant\EventParticipantRepository;
use App\Persistance\Repositories\User\UserRepository;

class EventParticipantService
{
    private $eventParticipantRepository;
    private $userRepository;

    public function __construct(EventParticipantRepository $eventParticipantRepository, UserRepository $userRepository)
    {
        $this->eventParticipantRepository = $eventParticipantRepository;
        $this->userRepository = $userRepository;
    }

    /**@return EventParticipant[]*/
    public function getAllForEvent(int $eventId):array
    {
        return $this->eventParticipantRepository->getAllByEventId($eventId);
    }

    public function confirmApply(int $participantId)
    {
        /**@var $participant EventParticipant*/
        $participant = $this->eventParticipantRepository->get($participantId);
        $participant->doConfirm();
        $this->eventParticipantRepository->update($participant);
    }

    public function delete(int $participantId)
    {
        $this->eventParticipantRepository->delete($participantId);
    }

}