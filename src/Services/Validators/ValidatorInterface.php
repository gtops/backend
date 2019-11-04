<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 25.10.2019
 * Time: 1:55
 */

namespace App\Services\Validators;


Interface ValidatorInterface
{
    public function getErrors($args, $options = null):array;
}