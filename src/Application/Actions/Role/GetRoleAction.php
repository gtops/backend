<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 03.11.2019
 * Time: 22:28
 */

namespace App\Application\Actions\Role;


use App\Application\Actions\Action;
use App\Domain\DomainException\DomainRecordNotFoundException;
use Monolog\Logger;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpBadRequestException;
use App\Persistance\Repositories\Role\RoleRepository;

class GetRoleAction extends Action
{

    /**
     *
     * * @SWG\Get(
     *   path="/role",
     *   summary="получение всех ролей(кроме GLOBAL)",
     *   operationId="получение всех ролей(кроме GLOBAL)",
     *   tags={"Role"},
     *   @SWG\Response(response=200, description="OK", @SWG\Schema(
     *          @SWG\Property(property="roles", type="array", @SWG\Items(
     *              @SWG\Property(property="role_id", type="integer"),
     *              @SWG\Property(property="name_of_role", type="string")
     *          )
     *     ))),
     * )
     *
     */

    /**
     * @return Response
     * @throws DomainRecordNotFoundException
     * @throws HttpBadRequestException
     */
    public function __construct()
    {
    }

    protected function action(): Response
    {
        $roleRep = new RoleRepository();
        $this->response->getBody()->write(json_encode(['roles' => $roleRep->getRoles()]));
        return $this->response;
    }
}