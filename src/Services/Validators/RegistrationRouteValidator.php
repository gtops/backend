<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 04.11.2019
 * Time: 19:52
 */

namespace App\Services\Validators;


use App\Application\Actions\ActionError;
use App\Persistance\Repositories\User\RegistrationToken;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationRouteValidator extends Validator implements ValidatorInterface
{

    public function getErrors($args, $options = null): array
    {
        $errors = [];

        if (!isset($args['token'], $args['name'], $args['password'])){
            $errors[] = new ActionError(ActionError::VALIDATION_ERROR, 'not all parameters passed');
        }

        if (count($errors) > 0){
            return $errors;
        }

        $violations = $this->validator->validate($args['password'],[
            new Length(['min' => 6]),
            new NotBlank()
        ]);

        if (count($violations) != 0){
            $errors[] = new ActionError(ActionError::VALIDATION_ERROR, $violations[0]->getMessage());
        }

        $violations = $this->validator->validate($args['name'],[
            new Length(['min' => 1]),
            new NotBlank()
        ]);

        if (count($violations) != 0){
            $errors[] = new ActionError(ActionError::VALIDATION_ERROR, $violations[0]->getMessage());
        }



        $tokRep = new RegistrationToken();

        try{
            $tokenDataFromDb = $tokRep->getTokenFromDB($args['token']);
            if (!isset($tokenDataFromDb[0]->token) || $tokenDataFromDb[0]->dateTimeToDelete < (new \DateTime())->format('Y-m-d H:i:s')){
                $errors[] = new ActionError(ActionError::BAD_REQUEST, 'invalid token');
            }
        }catch (\Exception $err){
            $errors[] = new ActionError(ActionError::BAD_REQUEST, 'invalid token');
        }

        return $errors;
    }
}