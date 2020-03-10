<?php
namespace App\Application\Actions\LocalAdmin;

use App\Application\Actions\Action;
use App\Application\Actions\ActionError;
use App\Application\Middleware\AuthorizeMiddleware;
use App\Services\LocalAdmin\LocalAdminService;
use App\Validators\LocalAdmin\LocalAdminValidator;
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

    /**
     *
     * @SWG\Post(
     *   path="/api/v1/organization/{id}/localAdmin",
     *   summary="добавляет локального администратора,который ранее не существовал в системе, с отправкой на почту ему данных об аккаунте",
     *   tags={"LocalAdmin"},
     *   @SWG\Parameter(in="header", name="Authorization", type="string", description="токен"),
     *   @SWG\Parameter(in="query", name="id", type="integer", description="id организации, к которой будем добавлять локального админа"),
     *   @SWG\Parameter(in="body", name="body", @SWG\Schema(ref="#/definitions/LocalAdminRequest")),
     *   @SWG\Response(response=200, description="OK", @SWG\Schema(@SWG\Property(property="id", type="integer"),)),
     *  @SWG\Response(response=400, description="Error", @SWG\Schema(
     *          @SWG\Property(property="errors", type="array", @SWG\Items(
     *              @SWG\Property(property="type", type="string"),
     *              @SWG\Property(property="description", type="string")
     *          ))
     *     )))
     * )
     *
     */
    public function addWithoutMessageToEmail(Request $request, Response $response, $args):Response
    {
        if ($this->tokenWithError($response, $request)){
            return $response->withStatus(401);
        }
        $userRole = $request->getHeader('userRole')[0];

        if ($userRole != AuthorizeMiddleware::GLOBAL_ADMIN){
            return $response->withStatus(403);
        }

        $rowParams = json_decode($request->getBody()->getContents(), true);
        $rowParams['organizationId'] = (int)$args['id'];
        $rowParams['localAdminId'] = -1;

        $errors = (new LocalAdminValidator())->validate($rowParams);
        if (count($errors) > 0){
            return $this->respond(400, ['errors' => $errors], $response);
        }

        $localAdminId = $this->localAdminService->addWithoutMessage($rowParams['name'], $rowParams['password'], $rowParams['email'], (int)$args['id'], $response);

        if ($localAdminId instanceof  ResponseInterface){
            return $localAdminId;
        }
        return $this->respond(200, ['id' => $localAdminId], $response);
    }

    /**
     *
     * @SWG\Post(
     *   path="/api/v1/organization/{id}/localAdmin/existingAccount",
     *   summary="добавляет локального администратора, который ранее существовал в системе",
     *   tags={"LocalAdmin"},
     *   @SWG\Parameter(in="header", name="Authorization", type="string", description="токен"),
     *   @SWG\Parameter(in="query", name="id", type="integer", description="id организации, к которой будем добавлять локального админа"),
     *   @SWG\Parameter(in="body", name="body", @SWG\Schema(@SWG\Property(property="email", type="string"))),
     *   @SWG\Response(response=200, description="OK", @SWG\Schema(@SWG\Property(property="id", type="integer"),)),
     *   @SWG\Response(response=404, description="Not found"),
     *  @SWG\Response(response=400, description="Error", @SWG\Schema(
     *          @SWG\Property(property="errors", type="array", @SWG\Items(
     *              @SWG\Property(property="type", type="string"),
     *              @SWG\Property(property="description", type="string")
     *          ))
     *     )))
     * )
     *
     */
    public function addExistingAccount(Request $request, Response $response, $args):Response
    {
        if ($this->tokenWithError($response, $request)){
            return $response->withStatus(401);
        }
        $userRole = $request->getHeader('userRole')[0];

        if ($userRole != AuthorizeMiddleware::GLOBAL_ADMIN){
            return $response->withStatus(403);
        }

        $rowParams = json_decode($request->getBody()->getContents(), true);

        if (!isset($rowParams['email'])){
            $response->getBody()->write(json_encode(['errors' => array(new ActionError(ActionError::BAD_REQUEST, 'поле email обязателен'))]));
            return $response->withStatus(400);
        }

        if(!filter_var($rowParams['email'], FILTER_VALIDATE_EMAIL)){
            $response->getBody()->write(json_encode(['errors' => array(new ActionError(ActionError::BAD_REQUEST, 'email не соответвует формату почты'))]));
            return $response->withStatus(400);
        }

        $localAdminId = $this->localAdminService->addFromExistingAccount($rowParams['email'], (int)$args['id'], $response);

        if ($localAdminId instanceof  Response){
            return $localAdminId;
        }
        return $this->respond(200, ['id' => $localAdminId], $response);
    }

    /**
     *
     * * @SWG\Delete(
     *   path="/api/v1/organization/{id}/localAdmin/{idLocalAdmin}",
     *   summary="удаляет локального админа, относящего к определенной организации, по id",
     *   tags={"LocalAdmin"},
     *   @SWG\Parameter(in="query", name="id", type="integer", description="id организации"),
     *   @SWG\Parameter(in="query", name="idLocalAdmin", type="integer", description="id локального админа"),
     *   @SWG\Parameter(in="header", name="Authorization", type="string", description="токен"),
     *   @SWG\Response(response=200, description="OK"),
     *  @SWG\Response(response=400, description="Error", @SWG\Schema(
     *          @SWG\Property(property="errors", type="array", @SWG\Items(
     *              @SWG\Property(property="type", type="string"),
     *              @SWG\Property(property="description", type="string")
     *          ))
     *     )))
     * )
     *
     */
    public function delete(Request $request, Response $response, $args)
    {
        if ($this->tokenWithError($response, $request)){
            return $response->withStatus(401);
        }

        $userRole = $request->getHeader('userRole')[0];
        if ($userRole != AuthorizeMiddleware::GLOBAL_ADMIN){
            return $response->withStatus(403);
        }

        $idOrganization = (int)$args['id'];
        $idLocalAdmin = (int)$args['idLocalAdmin'];
        $this->localAdminService->delete($idLocalAdmin, $idOrganization, $response);
        return $response;
    }

    /**
     *
     * * @SWG\Get(
     *   path="/api/v1/organization/{id}/localAdmin/{idLocalAdmin}",
     *   summary="получение локального админа по id, относящийся к конекртной организации",
     *   tags={"LocalAdmin"},
     *   @SWG\Parameter(in="query", name="id", type="integer", description="id организации"),
     *   @SWG\Parameter(in="query", name="idLocalAdmin", type="integer", description="id локального админа"),
     *   @SWG\Response(response=200, description="OK",
     *          @SWG\Schema(ref="#/definitions/LocalAdminResponse")
     *   ),
     *  @SWG\Response(response=404, description="Not found"),
     *  @SWG\Response(response=400, description="Error", @SWG\Schema(
     *          @SWG\Property(property="errors", type="array", @SWG\Items(
     *              @SWG\Property(property="type", type="string"),
     *              @SWG\Property(property="description", type="string")
     *          ))
     *     )))
     * )
     *
     */
    public function get(Request $request, Response $response, $args)
    {
        if ($this->tokenWithError($response, $request)){
            return $response->withStatus(401);
        }
        $userRole = $request->getHeader('userRole')[0];

        if ($userRole != AuthorizeMiddleware::GLOBAL_ADMIN){
            return $response->withStatus(403);
        }

        $idOrganization = (int)$args['id'];
        $idLocalAdmin = (int)$args['idLocalAdmin'];

        $localAdmin = $this->localAdminService->get($idLocalAdmin, $idOrganization);

        if ($localAdmin == null){
            return $response->withStatus(404);
        }

        $localAdminInArray = $localAdmin->toArray();
        unset($localAdminInArray['password']);
        return $this->respond(200, $localAdminInArray, $response);
    }

    /**
     *
     * * @SWG\Get(
     *   path="/api/v1/organization/{id}/localAdmin",
     *   summary="получение всех существующиъх локальных администраторов, относящихся к определенной организации",
     *   tags={"LocalAdmin"},
     *   @SWG\Parameter(in="query", name="id", type="integer", description="id организации"),
     *   @SWG\Response(response=200, description="OK",
     *          @SWG\Property(type="array", @SWG\Items(ref="#/definitions/LocalAdminResponse"))
     *   ),
     *  @SWG\Response(response=404, description="Not found"),
     *  @SWG\Response(response=400, description="Error", @SWG\Schema(
     *          @SWG\Property(property="errors", type="array", @SWG\Items(
     *              @SWG\Property(property="type", type="string"),
     *              @SWG\Property(property="description", type="string")
     *          ))
     *     )))
     * )
     *
     */
    public function getAll(Request $request, Response $response, $args)
    {
        if ($this->tokenWithError($response, $request)){
            return $response->withStatus(401);
        }
        $userRole = $request->getHeader('userRole')[0];

        if ($userRole != AuthorizeMiddleware::GLOBAL_ADMIN){
            return $response->withStatus(403);
        }

        $idOrganization = (int)$args['id'];
        $localAdmins = $this->localAdminService->getAll($idOrganization);

        if ($localAdmins == null){
            return $response->withStatus(404);
        }

        return $this->respond(200, $localAdmins, $response);
    }

    public function update(Request $request, Response $response, $args)
    {

    }

    public function add(Request $request, Response $response, $args)
    {

    }
}