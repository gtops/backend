<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 17.10.2019
 * Time: 10:57
 */

namespace App\Application\Actions\Swagger;


use App\Application\Actions\Action;
use App\Domain\DomainException\DomainRecordNotFoundException;
use App\Swagger\SwaggerWatcher;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpBadRequestException;

class SwaggerAction extends Action
{
    private $pathToProject;

    public function __construct($pathToProject)
    {
        $this->pathToProject = $pathToProject;
    }

    /**
     * @return Response
     * @throws DomainRecordNotFoundException
     * @throws HttpBadRequestException
     */
    protected function action(): Response
    {
        $swaggerWatcher = new SwaggerWatcher($this->pathToProject);
        $this->response->getBody()->write($swaggerWatcher->getDocumentation());
        return $this->response;
    }
}