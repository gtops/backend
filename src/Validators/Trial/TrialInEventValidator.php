<?php


namespace App\Validators\Trial;


use App\Validators\BaseValidator;

class TrialInEventValidator extends BaseValidator
{
    protected function addSpecificRules(array &$params, array $options = null)
    {
        $params = $this->getInitedParams($params);
        $this->addNotNullNotBlankRules(['trialId', 'sportObjectId']);
        $this->addIntTypeRule(['trialId', 'sportObjectId']);
    }

    private function getInitedParams(array $params)
    {
        return [
            'trialId' => $params['trialId'] ?? null,
            'sportObjectId' => $params['sportObjectId'] ?? null
        ];
    }
}