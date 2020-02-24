<?php

namespace App\Validators\LocalAdmin;
use App\Validators\BaseValidator;

class LocalAdminValidator extends BaseValidator
{
    protected function addSpecificRules(array &$params, array $options = null)
    {
        $params = $this->initParams($params);
        $this->addNotNullNotBlankRules(['organizationId', 'localAdminId', 'email', 'password', 'name']);
        $this->addIntTypeRule(['organizationId', 'localAdminId']);
        $this->addStringRule([ 'email', 'password', 'name']);
        $this->addEmailRule(['email']);
    }

    private function initParams(array $params)
    {
        $params['organizationId'] = $params['organizationId'] ?? null;
        $params['name'] = $params['name'] ?? null;
        $params['email'] = $params['email'] ?? null;
        $params['password'] = $params['password'] ?? null;
        $params['localAdminId'] = $params['localAdminId'] ?? null;
        //$params['organizationId'] = $params['organizationId'] ?? null;
        return $params;
    }
}