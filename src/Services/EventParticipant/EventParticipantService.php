<?php

namespace App\Services\EventParticipant;
use App\Domain\Models\EventParticipant\EventParticipant;
use App\Persistance\Repositories\EventParticipant\EventParticipantRepository;
use App\Persistance\Repositories\Team\TeamRepository;
use App\Persistance\Repositories\User\UserRepository;

class EventParticipantService
{
    private $eventParticipantRepository;
    private $userRepository;
    private $teamRepository;

    public function __construct(EventParticipantRepository $eventParticipantRepository, UserRepository $userRepository, TeamRepository $teamRepository)
    {
        $this->eventParticipantRepository = $eventParticipantRepository;
        $this->userRepository = $userRepository;
        $this->teamRepository = $teamRepository;
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

    public function addToTeam(string $userEmail, bool $confirmed, $teamId)
    {
        $team = $this->teamRepository->get($teamId);
        $user = $this->userRepository->getByEmail($userEmail);
        $eventParticipant = new EventParticipant(-1, $team->getEventId(), $user->getId(), $confirmed, $user, $teamId);
        return $this->eventParticipantRepository->add($eventParticipant);
    }

    public function getAllForTeam(int $teamId)
    {
        return $this->eventParticipantRepository->getAllFilteredByTeamId($teamId);
    }

    public function addToEvent($emailOfUserToAdd, bool $false, int $eventId)
    {
        $user = $this->userRepository->getByEmail($emailOfUserToAdd);
        $eventParticipant = new EventParticipant(-1, $eventId, $user->getId(), false, $user, null);
        return $this->eventParticipantRepository->add($eventParticipant);
    }

}