<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 04.11.2019
 * Time: 16:43
 */

namespace App\Services\Validators;


use App\Application\Actions\ActionError;
use App\Persistance\ModelsEloquant\Role\Role;
use App\Persistance\Repositories\Role\RoleRepository;
use App\Services\Token\Token;
use Monolog\Logger;

class SendInviteValidator extends Validator implements  ValidatorInterface
{
    /**
     * @param $args
     * @param null $options
     * @return array
     */
    public function getErrors($args, $options = null): array
    {
        $errors = [];

        if (!isset($args['email'], $args['role'], $args['token'])){
            $errors[] = new ActionError(ActionError::VALIDATION_ERROR, 'not all parameters passed');
        }

        if (count($errors) > 0){
            return $errors;
        }

        $roleRep = new RoleRepository();
        $roles = $roleRep->getRoles();

        $roleNotFound = true;
        foreach($roles as $role){
            if ($role['name_of_role'] == $args['role']){
                $roleNotFound = false;
            }
        }

        if ($roleNotFound){
            $errors[] = new ActionError(ActionError::VALIDATION_ERROR, 'role not found');
        }

        /** @var Token  */
        $tokenHandler = $options['tokenHandler'];

        try {
            $decodedToken = (array)$tokenHandler->getDecodedToken($args['token']);
        }catch (\Exception $err){
            $errors[] = new ActionError(ActionError::VALIDATION_ERROR, 'invalid token');
            return $errors;
        }

        if($tokenHandler->isOldToken($decodedToken)){
            $errors[] = new ActionError(ActionError::VALIDATION_ERROR, 'invalid token');
        }

        if ($decodedToken['role'] != "Глобальный администратор"){
            $errors[] = new ActionError(ActionError::UNAUTHENTICATED, 'access denied');
        }

        return $errors;
    }
}