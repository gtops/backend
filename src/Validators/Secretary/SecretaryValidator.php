<?php
namespace App\Validators\Secretary;

use App\Validators\BaseValidator;

class SecretaryValidator extends BaseValidator
{
    protected function addSpecificRules(array &$params, array $options = null)
    {
        $params = $this->initParams($params);
        $this->addNotNullNotBlankRules(['organizationId', 'eventId', 'email', 'password', 'name', 'dateOfBirth', 'gender']);
        $this->addIntTypeRule(['eventId', 'localAdminId', 'gender']);
        $this->addStringRule([ 'email', 'password', 'name']);
        $this->addEmailRule(['email']);
        $this->addDateTypeRule(['dateOfBirth']);
        $this->addInChoiceRule(['gender'], [0, 1]);
    }

    private function initParams(array $params)
    {
        $params['eventId'] = $params['eventId'] ?? null;
        $params['name'] = $params['name'] ?? null;
        $params['email'] = $params['email'] ?? null;
        $params['password'] = $params['password'] ?? null;
        $params['localAdminId'] = $params['localAdminId'] ?? null;
        $params['dateOfBirth'] = $params['dateOfBirth'] ?? null;
        $params['gender'] = $params['gender'] ?? null;
        return $params;
    }
}