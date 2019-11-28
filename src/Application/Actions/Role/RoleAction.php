<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 28.11.2019
 * Time: 9:53
 */

namespace App\Application\Actions\Role;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\RequestInterface;

use App\Application\Actions\Action;

class RoleAction extends Action
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
    public function getList(array $params, Response $response):Response
    {

    }
}