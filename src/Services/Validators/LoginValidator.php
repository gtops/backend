<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 05.11.2019
 * Time: 1:50
 */

namespace App\Services\Validators;


use App\Application\Actions\ActionError;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class LoginValidator extends Validator implements ValidatorInterface
{

    public function getErrors($args, $options = null): array
    {
        $errors = [];

        if (!isset($args['email'], $args['password'])){
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

        $violations = $this->validator->validate($args['email'],[
            new Email(),
            new NotBlank()
        ]);

        if (count($violations) != 0){
            $errors[] = new ActionError(ActionError::VALIDATION_ERROR, $violations[0]->getMessage());
        }

        return $errors;
    }
}