<?php

namespace App\Persistance\Repositories\LocalAdmin;
use App\Domain\Models\IModel;
use App\Domain\Models\IRepository;
use App\Domain\Models\Organization;

class Event implements IRepository
{

    public function get(int $id): ?IModel
    {
        // TODO: Implement get() method.
    }

    /**
     * @inheritDoc
     */
    public function getAll(): ?array
    {
        // TODO: Implement getAll() method.
    }

    public function add(IModel $model): int
    {
        // TODO: Implement add() method.
    }

    public function delete(int $id)
    {
        // TODO: Implement delete() method.
    }

    public function update(Organization $organization)
    {
        // TODO: Implement update() method.
    }
}