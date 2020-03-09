<?php

namespace App\Validators\Event;
use App\Validators\BaseValidator;

class EventValidator extends BaseValidator
{
    protected function addSpecificRules(array &$params, array $options = null)
    {
        $this->addNotNullNotBlankRules(['name', 'organizationId', 'startDate', 'expirationDate']);
        $this->addNotNullRules(['description']);
        $params = $this->initParams($params);
        $this->addStringRule(['description', 'name']);
        $this->addIntTypeRule(['organizationId']);
        $this->addDateTypeRule(['startDate', 'expirationDate']);
    }

    private function initParams(array $params)
    {
        $params['organizationId'] = $params['organizationId'] ?? null;
        $params['name'] = $params['name'] ?? null;
        $params['description'] = $params['description'] ?? null;
        $params['startDate'] = $params['startDate'] ?? null;
        $params['expirationDate'] = $params['expirationDate'] ?? null;
        return $params;
    }
}