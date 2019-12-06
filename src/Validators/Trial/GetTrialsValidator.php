<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 06.12.2019
 * Time: 4:30
 */

namespace App\Validators\Trial;


use App\Validators\BaseValidator;

class GetTrialsValidator extends BaseValidator
{
    protected function addSpecificRules(array $params, array $options = null)
    {
        $this->addNotNullNotBlankRules('gender');
        $this->addNotNullNotBlankRules('age');
        $this->addGreaterThenRule('age', 7);
        $this->addInChoiceRule('gender', [0, 1]);
    }
}