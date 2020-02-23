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

class ResultRepository implements IRepository
{

    public function get(int $id): IModel
    {
        // TODO: Implement get() method.
    }

    /**
     * @inheritDoc
     */
    public function getAll(): array
    {
        // TODO: Implement getAll() method.
    }

    public function add(IModel $model)
    {
        // TODO: Implement add() method.
    }

    public function delete(int $id)
    {
        // TODO: Implement delete() method.
    }
}