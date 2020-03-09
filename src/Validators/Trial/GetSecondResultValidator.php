<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 06.12.2019
 * Time: 4:37
 */

namespace App\Validators\Trial;


use App\Validators\BaseValidator;

class GetSecondResultValidator extends BaseValidator
{
    protected function addSpecificRules(array &$params, array $options = null)
    {
        $this->addNotNullNotBlankRules(['firstResult', 'id']);
        $this->addIntTypeRule(['id']);
        $this->addGreaterThenRule(['id'], 0);
    }
}