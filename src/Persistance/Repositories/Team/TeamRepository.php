<?php

namespace App\Persistance\Repositories\Role;
use App\Domain\Models\IModel;
use App\Domain\Models\IRepository;
use App\Domain\Models\Team\Team;
use App\Persistance\ModelsEloquant\Team\Team as TeamPdo;

class TeamRepository implements IRepository
{
    public function getFilteredByEventId(int $id): ?IModel
    {
        $results = TeamPdo::query()->where('team_id', '=', $id)->get();
        if (count($results) == 0){
            return null;
        }

        return new Team($results[0]['team_id'],$results[0][ 'event_id'], $results[0]['name']);
    }

    /**
     * @inheritDoc
     */

    public function getAllForEvent(int $eventId):?array
    {
        $results = TeamPdo::query()->where('event_id', '=', $eventId)->get();
        if (count($results) == 0){
            return null;
        }

        $teams = [];
        foreach ($results as $result){
            $teams[] = new Team($result['team_id'], $result['event_id'], $result['name']);
        }
    }

    public function getAll(): ?array
    {
    }

    /**@return int
     * @var $model Team
     */
    public function add(IModel $model): int
    {
        $object = TeamPdo::query()->create([
            'event_id' => $model->getEventId(),
            'name' => $model->getName()
        ]);

        return $object->getAttribute('team_id');
    }

    public function delete(int $id)
    {
        TeamPdo::query()->where('team_id', '=', $id)->delete();
    }

    /**
     * @param Team $model
     */
    public function update(IModel $model)
    {
        TeamPdo::query()->where('team_id', '=', $model->getId())->update([
            'name' => $model->getName()
        ]);
    }
}