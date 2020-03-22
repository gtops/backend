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
        $this->addNotNullNotBlankRules(['password']);
        $this->addLengthRule(['password'], 6);
        $this->addStringRule(['password']);
    }

    private function initParams(array $params)
    {
        return [
            'password' => $params['password'] ?? null
        ];
    }

}