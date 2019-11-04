<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 04.11.2019
 * Time: 20:08
 */

namespace App\Services\Validators;


use Symfony\Component\Validator\Validation;

class Validator
{
    protected $validator;
    public function __construct()
    {
        $this->validator = Validation::createValidator();
    }
}