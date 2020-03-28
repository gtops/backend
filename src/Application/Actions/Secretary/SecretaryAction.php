<?php

namespace App\Application\Actions\Secretary;
use App\Application\Actions\Action;
use App\Application\Actions\ActionError;
use App\Application\Middleware\AuthorizeMiddleware;
use App\Domain\Models\User\UserCreater;
use App\Services\Secretary\SecretaryService;
use App\Services\Token\Token;
use App\Validators\Secretary\SecretaryValidator;
use Illuminate\Support\Facades\Date;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Symfony\Component\Validator\Constraints\DateTime;

class SecretaryAction extends Action
{
    private $secretaryService;

    public function __construct(SecretaryService $secretaryService)
    {
        $this->secretaryService = $secretaryService;
    }

    /**
     *
     * @SWG\Post(
     *   path="/api/v1/organization/{id}/event/{eventId}/secretary/existingAccount",
     *   summary="добавляет секретаря, из ранее существующих аккаунтов(локальный админ)",
     *   tags={"Secretary"},
     *   @SWG\Parameter(in="header", name="Authorization", type="string", description="токен"),
     *   @SWG\Parameter(in="query", name="id", type="integer", description="id организации"),
     *   @SWG\Parameter(in="query", name="eventId", type="integer", description="id мероприятия, право не редактирование которого добавляем"),
     *   @SWG\Parameter(in="body", name="body", @SWG\Schema(@SWG\Property(property="email", type="string"))),
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

    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return Response
     */
    public function addExistingAccount(Request $request, Response $response, $args):Response
    {
        if ($this->tokenWithError($response, $request)){
            return $response->withStatus(401);
        }
        $userRole = $request->getHeader('userRole')[0];
        $localAdminEmail = $request->getHeader('userEmail')[0];

        if ($userRole != AuthorizeMiddleware::LOCAL_ADMIN){
            return $response->withStatus(403);
        }

        $rowParams = json_decode($request->getBody()->getContents(), true);

        if (!isset($rowParams['email'])){
            $error = new ActionError(ActionError::BAD_REQUEST, 'поле email обязателен');
            $response->getBody()->write(json_encode(['errors' => array($error->jsonSerialize())]));
            return $response->withStatus(400);
        }

        if(!filter_var($rowParams['email'], FILTER_VALIDATE_EMAIL)){
            $error = new ActionError(ActionError::BAD_REQUEST, 'email не соответвует формату почты');
            $response->getBody()->write(json_encode(['errors' => array($error->jsonSerialize())]));
            return $response->withStatus(400);
        }

        $secretaryId = $this->secretaryService->addFromExistingAccount($localAdminEmail, $rowParams['email'], (int)$args['id'], (int)$args['eventId'], $response);

        if ($secretaryId instanceof  Response){
            return $secretaryId;
        }
        return $this->respond(200, ['id' => $secretaryId], $response);
    }

    /**
     *
     * * @SWG\Get(
     *   path="/api/v1/organization/{id}/event/{eventId}/secretary",
     *   summary="получение секретайрей, относящихся к определенному меропритию",
     *   tags={"Secretary"},
     *   @SWG\Parameter(in="query", name="id", type="integer", description="id организации"),
     *   @SWG\Parameter(in="query", name="eventId", type="integer", description="id мероприятия"),
     *   @SWG\Response(response=200, description="OK",
     *          @SWG\Schema(ref="#/definitions/secretaryResponse")
     *   ),
     *  @SWG\Response(response=400, description="Error", @SWG\Schema(
     *          @SWG\Property(property="errors", type="array", @SWG\Items(
     *              @SWG\Property(property="type", type="string"),
     *              @SWG\Property(property="description", type="string")
     *          ))
     *     )))
     * )
     */

    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return Response
     */
    public function get(Request $request, Response $response, $args):Response
    {
        if ($this->tokenWithError($response, $request)){
            return $response->withStatus(401);
        }
        $userRole = $request->getHeader('userRole')[0];
        $localAdminEmail = $request->getHeader('userEmail')[0];

        if ($userRole != AuthorizeMiddleware::LOCAL_ADMIN){
            return $response->withStatus(403);
        }

        $secretaries = $this->secretaryService->get((int)$args['id'], (int)$args['eventId'], $localAdminEmail, $response);
        if ($secretaries == null){
            return $this->respond(200, [], $response);
        }

        if ($secretaries instanceof  Response){
            return $secretaries;
        }

        $secretariesInArray = [];

        foreach ($secretaries as $secretary){
            $secretariesInArray[] = $secretary->toArray();
        }

        return $this->respond(200, $secretariesInArray, $response);
    }

    /**
     *
     * * @SWG\Delete(
     *   path="/api/v1/organization/{id}/event/{eventId}/secretary{secretaryId}",
     *   summary="удаляет секретаря от определенной организации(локальный админ)",
     *   tags={"Secretary"},
     *   @SWG\Parameter(in="query", name="id", type="integer", description="id организации"),
     *   @SWG\Parameter(in="query", name="eventId", type="integer", description="id мероприятия, от которого удаляем секретаря"),
     *   @SWG\Parameter(in="query", name="secretaryId", type="integer", description="id секретаря, которого удаляем"),
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

    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return Response
     */
    public function delete(Request $request, Response $response, $args):Response
    {
        if ($this->tokenWithError($response, $request)){
            return $response->withStatus(401);
        }

        $userRole = $request->getHeader('userRole')[0];
        $localAdminEmail = $request->getHeader('userEmail')[0];

        if ($userRole != AuthorizeMiddleware::LOCAL_ADMIN){
            return $response->withStatus(403);
        }

        $result = $this->secretaryService->delete((int)$args['id'], (int)$args['eventId'], (int)$args['secretaryId'], $localAdminEmail, $response);

        if ($result instanceof Response){
            return $result;
        }

        return  $response;
    }
}