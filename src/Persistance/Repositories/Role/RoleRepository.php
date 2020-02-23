<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 03.11.2019
 * Time: 22:30
 */

namespace App\Persistance\Repositories\Role;
use App\Domain\Models\IModel;
use App\Domain\Models\IRepository;
use App\Persistance\ModelsEloquant\Role\Role;
use Monolog\Logger;

class RoleRepository implements IRepository
{
    public function getRoles():array
    {
        $roles = [];
        $roleSql = Role::query()->where('name_of_role', '!=', 'Глобальный администратор')->get();
        foreach ($roleSql as $role){
            $roles[] = $role;
        }

        return $roles;
    }

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