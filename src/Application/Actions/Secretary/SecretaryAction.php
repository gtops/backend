<?php

namespace App\Application\Actions\Secretary;
use App\Application\Actions\Action;
use App\Application\Actions\ActionError;
use App\Application\Middleware\AuthorizeMiddleware;
use App\Services\Secretary\SecretaryService;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class SecretaryAction extends Action
{
    private $secretaryService;

    public function __construct(SecretaryService $secretaryService)
    {
        $this->secretaryService = $secretaryService;;
    }

    /**
     *
     * @SWG\Post(
     *   path="/api/v1/organization/{id}/event/{id}/secretary",
     *   summary="добавляет секретаря, с отправкой на почту ему данных об аккаунте",
     *   tags={"Secretary"},
     *   @SWG\Parameter(in="header", name="Authorization", type="string", description="токен"),
     *   @SWG\Parameter(in="query", name="id", type="integer", description="id организации"),
     *   @SWG\Parameter(in="query", name="eventId", type="integer", description="id мероприятия, право не редактирование которого добавляем"),
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
    public function add(Request $request, Response $response, $args):Response
    {

    }

    /**
     *
     * @SWG\Post(
     *   path="/api/v1/organization/{id}/event/{eventId}/secretary/existingAccount",
     *   summary="добавляет секретаря, из ранее существующих аккаунтов",
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
            $response->getBody()->write(json_encode(['errors' => array(new ActionError(ActionError::BAD_REQUEST, 'поле email обязателен'))]));
            return $response->withStatus(400);
        }

        if(!filter_var($rowParams['email'], FILTER_VALIDATE_EMAIL)){
            $response->getBody()->write(json_encode(['errors' => array(new ActionError(ActionError::BAD_REQUEST, 'email не соответвует формату почты'))]));
            return $response->withStatus(400);
        }

        $localAdminId = $this->secretaryService->addFromExistingAccount($localAdminEmail, $rowParams['email'], (int)$args['id'], (int)$args['eventId'], $response);

        if ($localAdminId instanceof  Response){
            return $localAdminId;
        }
        return $this->respond(200, ['id' => $localAdminId], $response);
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
    public function get()
    {

    }

    /**
     *
     * * @SWG\Delete(
     *   path="/api/v1/organization/{id}/event/{eventId}/secretary{secretaryId}",
     *   summary="удаляет секретаря от определенной организации",
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
    public function delete()
    {

    }
}