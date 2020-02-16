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
    protected function addSpecificRules(array $params, array $options = null)
    {
        $this->addNotNullNotBlankRules(['password', 'name', 'token']);
        $this->addStringRule(['name', 'password', 'token']);
        $this->addLengthRule(['password'], 6);
    }
}