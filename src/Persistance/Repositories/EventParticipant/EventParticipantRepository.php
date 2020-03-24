<?php

namespace App\Persistance\Repositories\EventParticipant;
use App\Domain\Models\EventParticipant\EventParticipant;
use App\Domain\Models\IModel;
use App\Domain\Models\IRepository;
use App\Persistance\ModelsEloquant\EventParticipant\EventParticipant as EventParticipantPDO;


class EventParticipantRepository implements IRepository
{

    public function get(int $id): ?IModel
    {
        $result = EventParticipantPDO::query()
            ->where('event_participant_id', '=', $id)
            ->get();

        if (count($result) == 0){
            return null;
        }

        return $this->getEventParticipant($result[0]);
    }

    /**
     * @inheritDoc
     */
    public function getAll(): ?array
    {
        // TODO: Implement getAll() method.
    }

    /**@var $model EventParticipant*/
    public function add(IModel $model): int
    {
        return EventParticipantPDO::query()->create([
            'event_id' => $model->getEventId(),
            'team_id' => $model->getTeamId(),
            'confirmed' => $model->isConfirmed(),
            'user_id' => $model->getUserId()
        ])->getAttribute('event_participant_id');
    }

    public function delete(int $id)
    {
        EventParticipantPDO::query()
            ->where('event_participant_id', '=', $id)
            ->delete();
    }

    /**@var $model EventParticipant*/
    public function update(IModel $model)
    {
        EventParticipantPDO::query()->where('event_participant_id', '=', $model->getEventParticipantId())->update([
            'team_id' => $model->getTeamId(),
            'event_id' => $model->getEventId(),
            'confirmed' => $model->isConfirmed(),
            'user_id' => $model->getUserId()
        ]);
    }

    public function getByEmail(string $email, $eventId):?EventParticipant
    {
        $result = EventParticipantPDO::query()
                ->join('user', 'event_participant.user_id', '=', 'user.user_id')
                ->where([
                    'user.email' => $email,
                    'event_id' => $eventId
                ])
                ->get([
                    'event_participant_id',
                    'event_participant.user_id',
                    'event_id',
                    'confirmed',
                    'team_id'
                ]);

        if (count($result) == 0){
            return null;
        }

        return $this->getEventParticipant($result[0]);
    }

    private function getEventParticipant($params):EventParticipant
    {
        return new EventParticipant
        (
            $params['event_participant_id'],
            $params['event_id'],
            $params['user_id'],
            $params['confirmed'],
            $params['team_id']
        );
    }

    public function getAllByEventId($eventId)
    {
        $results = EventParticipantPDO::query()->where('event_id', '=', $eventId)->get();
        $response = [];
        foreach ($results as $result){
            $response[] = $this->getEventParticipant($result);
        }

        return $response;
    }
}