<?php
declare(strict_types=1);

namespace App\Application\Actions;
use Symfony\Component\Validator\Constraints as Assert;
use Psr\Log\LoggerInterface;
use Symfony\Component\Validator\Validation;
use Psr\Http\Message\ResponseInterface as Response;

abstract class Action
{
    protected function respond(int $status, array $data = null, Response $response):Response
    {
        if ($data){
            $response->getBody()->write(json_encode($data));
        }

        return $response->withStatus($status);
    }
}
