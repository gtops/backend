<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 25.10.2019
 * Time: 1:59
 */

namespace App\Services\Validators;


use App\Application\Actions\ActionError;

class getTrialsRouteValidator extends Validator implements ValidatorInterface
{
    public function getErrors($args, $options = null): array
    {
        $errors = [];

        if (!isset($args['gender'], $args['age'])){
            $errors[] = new ActionError(ActionError::VALIDATION_ERROR, 'not all parameters passed');
        }

        if (count($errors) > 0){
            return $errors;
        }

        $gender = $args['gender'];
        $age = $args['age'];

        if (!is_numeric($gender) || !is_numeric($age)){
            $errors[] = new ActionError(ActionError::VALIDATION_ERROR, 'expected numeric argument');
        }

        if ($gender != 0 && $gender != 1){
            $errors[] = new ActionError(ActionError::VALIDATION_ERROR, 'incorrect gender, 0 - female, 1 - male');
        }

        if ($age < 8){
            $errors[] = new ActionError(ActionError::VALIDATION_ERROR, 'so low $age, start from 8');
        }

        return $errors;
    }
}