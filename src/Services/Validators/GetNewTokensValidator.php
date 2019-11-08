<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 08.11.2019
 * Time: 3:44
 */

namespace App\Services\Validators;


use App\Application\Actions\ActionError;
use App\Persistance\Repositories\User\RefreshToken;
use App\Services\Token\Token;
use Monolog\Logger;

class GetNewTokensValidator extends Validator implements ValidatorInterface
{

    public function getErrors($args, $options = null): array
    {
        $errors = [];

        if (!isset($args['refreshToken'])){
            $errors[] = new ActionError(ActionError::VALIDATION_ERROR, 'not all parameters passed');
        }

        if (count($errors) > 0){
            return $errors;
        }

        try {
            $decodedToken = (array)Token::getDecodedToken($args['refreshToken']);
        }catch (\Exception $err){
            $errors[] = new ActionError(ActionError::VALIDATION_ERROR, 'invalid token');
            return $errors;
        }

        if (Token::isOldToken($decodedToken)){
            $errors[] = new ActionError(ActionError::VALIDATION_ERROR, 'old token ');
        }

        $refreshTokenRep = new RefreshToken();

        if(!$refreshTokenRep->refreshTokenIsSet($args['refreshToken'])){
            $errors[] = new ActionError(ActionError::VALIDATION_ERROR, 'token not found');
        }

        return $errors;
    }
}