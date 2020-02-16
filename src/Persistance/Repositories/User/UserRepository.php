<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 04.11.2019
 * Time: 20:37
 */

namespace App\Persistance\Repositories\User;
use App\Persistance\ModelsEloquant\User\User as UserElaquent;
use App\Persistance\Repositories\Role;

class UserRepository
{
    public function userIsSetOnDBWithEmail($email)
    {
        if (UserElaquent::query()->where('email', '=', $email)->count() == 0){
            return false;
        }

        return true;
    }

    public function userIsSetOnDb($email, $password)
    {
        if (UserElaquent::query()
                ->where('email', '=', $email)
                ->where('password', '=', $password)
                ->count() == 0){
            return false;
        }

        return true;
    }

    public function getRoleOfUser($email)
    {
        $roles = UserElaquent::query()
            ->leftJoin('role', 'user.role_id', '=', 'role.role_id')
            ->where('user.email', '=', $email)
            ->get();

        return $roles[0]->name_of_role;
    }

    public function createUser($data)
    {
        $roleRep = new Role\RoleRepository();
        $roles = $roleRep->getRoles();

        $roleId = 0;
        foreach ($roles as $role){
            if ($role['name_of_role'] == $data['role']){
                $roleId = (int)$role['role_id'];
            }
        }

        UserElaquent::query()->create([
            'name' => $data['name'],
            'password' => $data['password'],
            'email' => $data['email'],
            'role_id' => $roleId,
            'is_activity' => 1,
            'registration_date' => (new \DateTime())->format('Y-m-d H:i:s')
        ]);
    }
}