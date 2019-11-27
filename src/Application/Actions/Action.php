<?php
declare(strict_types=1);

namespace App\Application\Actions;
use Symfony\Component\Validator\Constraints as Assert;
use Psr\Log\LoggerInterface;
use Symfony\Component\Validator\Validation;
use Psr\Http\Message\ResponseInterface as Response;

abstract class Action
{
    /**
     * @var LoggerInterface
     */

    protected $validator;

    protected function getErrors(Assert\Collection $collection, $params)
    {
        $errorsValidator = $this->validator->validate($params, $collection);

        $errors = [];

        foreach ($errorsValidator as $item) {
            $errors[] = new ActionError(ActionError::VALIDATION_ERROR, $item->getMessage());
        }

        return $errors;
    }

    protected function respond(int $status, array $data = null, Response $response):Response
    {
        if ($data){
            $response->getBody()->write(json_encode($data));
        }

        return $response->withStatus($status);
    }

    protected function __construct()
    {
        $this->validator = Validation::createValidator();
    }
}
