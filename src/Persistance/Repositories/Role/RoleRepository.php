<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 03.11.2019
 * Time: 22:30
 */

namespace App\Persistance\Repositories\Role;
use App\Persistance\ModelsEloquant\Role\Role;
use Monolog\Logger;

class RoleRepository
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
}