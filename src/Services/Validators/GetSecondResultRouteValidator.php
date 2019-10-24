<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 25.10.2019
 * Time: 2:38
 */

namespace App\Services\Validators;


use App\Application\Actions\ActionError;

class GetSecondResultRouteValidator implements ValidatorInterface
{
    public function getErrors($args): array
    {
        $errors = [];

        if (!isset($args['firstResult'], $args['trialId'])){
            $errors[] = new ActionError(ActionError::VALIDATION_ERROR, 'not all parameters passed');
        }

        if (count($errors) > 0){
            return $errors;
        }

        $firstResult = $args['firstResult'];
        $trialId = $args['trialId'];

        if (!is_numeric($firstResult) || !is_numeric($trialId)){
            $errors[] = new ActionError(ActionError::VALIDATION_ERROR, 'expected numeric argument');
        }

        return $errors;
    }
}