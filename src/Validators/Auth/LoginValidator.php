<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 06.12.2019
 * Time: 4:17
 */

namespace App\Validators\Auth;


use App\Validators\BaseValidator;

class LoginValidator extends BaseValidator
{
    protected function addSpecificRules(array &$params, array $options = null)
    {
        $this->addNotNullNotBlankRules(['password', 'email']);
        $this->addEmailRule(['email']);
        $this->addLengthRule(['password'], 6);
        $this->addStringRule(['password']);
    }
}