<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 06.12.2019
 * Time: 4:08
 */

namespace App\Validators\Auth;


use App\Validators\BaseValidator;

class RegistrationValidator extends BaseValidator
{
    protected function addSpecificRules(array &$params, array $options = null)
    {
        $params = $this->getInitedParams($params);
        $this->addNotNullNotBlankRules(['password']);
        $this->addLengthRule(['password'], 6);
        $this->addStringRule(['password']);
    }

    private function getInitedParams(array $params)
    {
        return [
            'password' => $params['password'] ?? null
        ];
    }

}