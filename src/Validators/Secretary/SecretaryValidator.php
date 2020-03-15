<?php
namespace App\Validators\Secretary;

use App\Domain\Models\Secretary\Secretary;
use App\Persistance\Repositories\Secretary\SecretaryRepository;
use App\Validators\BaseValidator;

class SecretaryValidator extends BaseValidator
{
    private $secretaryRepository;
    public function __construct(SecretaryRepository $secretaryRepository)
    {
        $this->secretaryRepository = $secretaryRepository;
    }

    public function add(Secretary $secretary):int
    {

    }
}