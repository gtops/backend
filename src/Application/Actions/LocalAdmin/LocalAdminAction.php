<?php
namespace App\Application\Actions\LocalAdmin;

use App\Application\Actions\Action;
use App\Services\LocalAdmin\LocalAdminService;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ResponseInterface as Response;

class LocalAdminAction extends Action
{
    private $localAdminService;

    public function __construct(LocalAdminService $localAdminService)
    {
        $this->localAdminService = $localAdminService;
    }

    public function addWithoutMessageToEmail(Request $request, Response $response, $args):Response
    {
        //TODO валидация на то, что эту операцию пытается делать глобальный администратор
        $rowParams = json_decode($request->getBody()->getContents(), true);
        //TODO валидация объекта локальный администратор
        $localAdminId = $this->localAdminService->addWithoutMessage($rowParams['name'], $rowParams['password'], $rowParams['email'], (int)$args['id'], $response);

        if ($localAdminId instanceof  ResponseInterface){
            return $response;
        }
        return $this->respond(200, ['id' => $localAdminId], $response);
    }

    public function delete(Request $request, Response $response, $args)
    {
        $idOrganization = (int)$args['id'];
        $idLocalAdmin = (int)$args['idLocalAdmin'];
        $this->localAdminService->delete($idLocalAdmin, $idOrganization, $response);
        return $response;
    }
}