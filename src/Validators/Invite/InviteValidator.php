<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 06.12.2019
 * Time: 3:44
 */

namespace App\Validators\Invite;


use App\Validators\BaseValidator;

class InviteValidator extends BaseValidator
{
    protected function addSpecificRules(array $params, array $options = null)
    {
        $this->addNotNullNotBlankRules(['userRole', 'userEmail', 'email', 'role']);
        $this->addStringRule(['role']);
        $this->addEqualRule(['userRole'], 'Глобальный администратор');
        $this->addEmailRule(['email']);
    }
}