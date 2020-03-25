<?php

namespace App\Application\Actions\EventParticipant;

use App\Application\Actions\Action;
use App\Application\Actions\ActionError;
use App\Services\AccessService\AccessService;
use App\Services\EventParticipant\EventParticipantService;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class EventParticipantAction extends Action
{
    private $accessService;
    private $eventParticipantService;

    public function __construct(AccessService $accessService, EventParticipantService $eventParticipantService)
    {
        $this->accessService = $accessService;
        $this->eventParticipantService = $eventParticipantService;
    }

    /**
     *
     * @SWG\Post(
     *   path="/api/v1/team/{teamId}/participant",
     *   summary="добавление участника в команду(тренер той команды, которая передана или же локальный админ и секретарь данного мероприятия)",
     *   tags={"ParticipantEvent"},
     *   @SWG\Parameter(in="header", name="Authorization", type="string", description="токен"),
     *   @SWG\Parameter(in="query", name="teamId", type="integer", description="id команды, к которой добавляем участника"),
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
    public function add(Request $request, Response $response, $args):Response
    {
        if ($this->tokenWithError($response, $request)) {
            return $response->withStatus(401);
        }

        $userRole = $request->getHeader('userRole')[0];
        $userEmail = $request->getHeader('userEmail')[0];
        $params = json_decode($request->getBody()->getContents(), true);
        $emailOfUserToAdd = $params['email'];

        if (!filter_var($emailOfUserToAdd, FILTER_VALIDATE_EMAIL)){
            $error = new ActionError(ActionError::BAD_REQUEST, 'email не соответвует формату почты');
            $response->getBody()->write(json_encode(['errors' => array($error->jsonSerialize())]));
            return $response->withStatus(400);
        }

        $teamId = (int)$args['teamId'];
        $access = $this->accessService->hasAccessAddParticipantToTeam($userEmail, $userRole, $teamId, $emailOfUserToAdd);

        if ($access === false){
            return $response->withStatus(403);
        }else if ($access !== true){
            return $this->respond(400, $access, $response);
        }

        $id = $this->eventParticipantService->addToTeam($emailOfUserToAdd, false, $teamId);
        return $this->respond(200, ['id' => $id], $response);
    }

    /**
     *
     * * @SWG\Get(
     *   path="/api/v1/event/{eventId}/participant",
     *   summary="получение участников мероприятия",
     *   tags={"ParticipantEvent"},
     *   @SWG\Parameter(in="query", name="eventId", type="integer", description="id мероприятия"),
     *   @SWG\Response(response=200, description="OK",
     *          @SWG\Property(type="array", @SWG\Items(ref="#/definitions/participantEvent"))
     *   ),
     * )
     *
     */
    public function getAllForEvent(Request $request, Response $response, $args):Response
    {
        $eventId = (int) $args['eventId'];
        $participants = $this->eventParticipantService->getAllForEvent($eventId);

        $participantsInArray = [];

        foreach ($participants as $participant){
            $participantsInArray[] = $participant->toArray();
        }

        return $this->respond(200, $participantsInArray, $response);
    }

    /**
     *
     * @SWG\Post(
     *   path="/api/v1/event/{eventId}/participant/{participantId}",
     *   summary="локальный админ или секретарь могут приянять заявку от participantId которй подал его на участие в мероприятии(локальный админ и секретарь этого мероприятия)",
     *   tags={"ParticipantEvent"},
     *   @SWG\Parameter(in="header", name="Authorization", type="string", description="токен"),
     *   @SWG\Parameter(in="query", name="eventId", type="integer", description="id мероприятия"),
     *   @SWG\Parameter(in="query", name="participantId", type="integer", description="id владельца заявки"),
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
    public function confirmApply(Request $request, Response $response, $args):Response
    {
        if ($this->tokenWithError($response, $request)) {
            return $response->withStatus(401);
        }

        $userRole = $request->getHeader('userRole')[0];
        $userEmail = $request->getHeader('userEmail')[0];

        $participantId = (int)$args['participantId'];

        //todo доделать принятие заявки в плане разрешений
        $access = $this->accessService->hasAccessWorkWithParticipant($userEmail, $participantId, $userRole);

        if ($access === false){
            return $response->withStatus(403);
        }else if ($access !== true){
            /**@var $access array*/
            return $this->respond(400, $access, $response);
        }

        $this->eventParticipantService->confirmApply($participantId);
        return $response;
    }

    /**
     *
     * @SWG\Delete(
     *   path="/api/v1/event/{eventId}/participant/{participantId}",
     *   summary="Удаляет участника из мероприятия(локальный админ и секретарь этого мероприятия)",
     *   tags={"ParticipantEvent"},
     *   @SWG\Parameter(in="header", name="Authorization", type="string", description="токен"),
     *   @SWG\Parameter(in="query", name="eventId", type="integer", description="id мероприятия"),
     *   @SWG\Parameter(in="query", name="participantId", type="integer", description="id владельца заявки"),
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
    public function deleteParticipant(Request $request, Response $response, $args):Response
    {
        if ($this->tokenWithError($response, $request)) {
            return $response->withStatus(401);
        }

        $userRole = $request->getHeader('userRole')[0];
        $userEmail = $request->getHeader('userEmail')[0];

        $participantId = (int)$args['participantId'];

        //todo доделать принятие заявки в плане разрешений
        $access = $this->accessService->hasAccessWorkWithParticipant($userEmail, $participantId, $userRole);

        if ($access === false){
            return $response->withStatus(403);
        }else if ($access !== true){
            /**@var $access array*/
            return $this->respond(400, $access, $response);
        }

        $this->eventParticipantService->delete($participantId);
        return $response;
    }
}