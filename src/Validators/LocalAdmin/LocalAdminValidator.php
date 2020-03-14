<?php

namespace App\Validators\LocalAdmin;
use App\Validators\BaseValidator;

class LocalAdminValidator extends BaseValidator
{
    protected function addSpecificRules(array &$params, array $options = null)
    {
        $params = $this->initParams($params);
        $this->addNotNullNotBlankRules(['organizationId', 'localAdminId', 'email', 'password', 'name', 'dateOfBirth', 'gender']);
        $this->addIntTypeRule(['organizationId', 'localAdminId', 'gender']);
        $this->addStringRule([ 'email', 'password', 'name']);
        $this->addEmailRule(['email']);
        $this->addDateTypeRule(['dateOfBirth']);
        $this->addInChoiceRule(['gender'], [0, 1]);
    }

    private function initParams(array $params)
    {
        $params['organizationId'] = $params['organizationId'] ?? null;
        $params['name'] = $params['name'] ?? null;
        $params['email'] = $params['email'] ?? null;
        $params['password'] = $params['password'] ?? null;
        $params['localAdminId'] = $params['localAdminId'] ?? null;
        $params['dateOfBirth'] = $params['dateOfBirth'] ?? null;
        $params['gender'] = $params['gender'] ?? null;
        return $params;
    }
}