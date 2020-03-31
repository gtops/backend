<?php

namespace App\Application\Actions\Event;
use App\Application\Actions\Action;
use App\Application\Middleware\AuthorizeMiddleware;
use App\Domain\Models\Event\Event;
use App\Domain\Models\EventParticipant\EventParticipant;
use App\Services\AccessService\AccessService;
use App\Services\Event\EventService;
use App\Validators\Event\EventValidator;
use DateTime;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class EventAction extends Action
{
    private $eventService;
    private $accessService;

    public function __construct(EventService $eventService, AccessService $accessService)
    {
        $this->accessService = $accessService;
        $this->eventService = $eventService;
    }

    /**
     *
     * * @SWG\Get(
     *   path="/api/v1/event/forSecretary",
     *   summary="получение мероприятия дсотупные секретарю(секретарь)",
     *   tags={"Event"},
     *   @SWG\Parameter(in="header", name="Authorization", type="string", description="токен"),
     *   @SWG\Parameter(in="query", name="eventId", type="integer", description="id мероприятия"),
     *   @SWG\Response(response=200, description="OK",
     *           @SWG\Property(type="array", @SWG\Items(ref="#/definitions/eventResponse"))
     *   ),
     *   @SWG\Response(response=401, description=""),
     *   @SWG\Response(response=403, description="")
     * )
     *
     */
    public function getForSecretary(Request $request, Response $response, $args): Response
    {
        if ($this->tokenWithError($response, $request)) {
            return $response->withStatus(401);
        }

        $userRole = $request->getHeader('userRole')[0];
        $userEmail = $request->getHeader('userEmail')[0];

        if ($userRole != AuthorizeMiddleware::SECRETARY){
            return $response->withStatus(403);
        }

        $events = $this->eventService->getForSecretary($userEmail);
        $eventsToResponse = [];

        foreach ($events as $event){
            $eventsToResponse[] = $event->toArray();
        }

        return $this->respond(200, $eventsToResponse, $response);
    }

    /**
     *
     * * @SWG\Get(
     *   path="/api/v1/event/forUser",
     *   summary="получение мероприятий, в которых он участвует",
     *   tags={"Event"},
     *   @SWG\Parameter(in="header", name="Authorization", type="string", description="токен"),
     *   @SWG\Parameter(in="query", name="eventId", type="integer", description="id мероприятия"),
     *   @SWG\Response(response=200, description="OK",
     *           @SWG\Property(type="array", @SWG\Items(ref="#/definitions/eventsForUser"))
     *   ),
     *   @SWG\Response(response=401, description=""),
     *   @SWG\Response(response=403, description="")
     * )
     *
     */
    public function getForUser(Request $request, Response $response, $args): Response
    {
        if ($this->tokenWithError($response, $request)) {
            return $response->withStatus(401);
        }

        $userEmail = $request->getHeader('userEmail')[0];

        $events = $this->eventService->getForUser($userEmail);
        return $this->respond(200, $events, $response);
    }

    /**
     *
     * @SWG\Post(
     *   path="/api/v1/event/{eventId}/apply",
     *   summary="пользователь подает заявку на участие в мероприятии",
     *   tags={"ParticipantEvent"},
     *   @SWG\Parameter(in="header", name="Authorization", type="string", description="токен"),
     *   @SWG\Parameter(in="query", name="eventId", type="integer", description="id мероприятия"),
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
    public function apply(Request $request, Response $response, $args): Response
    {
        if ($this->tokenWithError($response, $request)) {
            return $response->withStatus(401);
        }

        $userEmail = $request->getHeader('userEmail')[0];
        $eventId = (int)$args['eventId'];
        $access = $this->accessService->hasAccessApplyToEvent($eventId, $userEmail);

        if ($access === false){
            return $response->withStatus(403);
        }else if ($access !== true){
            /**@var $access array*/
            return $this->respond(400, ['errors' => $access], $response);
        }

        $participantId = $this->eventService->applyToEvent($eventId, $userEmail, false);
        return $this->respond(200, ['id' => $participantId], $response);
    }

    /**
     *
     * @SWG\Post(
     *   path="/api/v1/organization/{id}/event",
     *   summary="добавляет мероприятие к организации локальным админом(локальный админ)",
     *   tags={"Event"},
     *   @SWG\Parameter(in="header", name="Authorization", type="string", description="токен"),
     *   @SWG\Parameter(in="query", name="id", type="integer", description="id организации, к которой будем добавлять мероприятия"),
     *   @SWG\Parameter(in="body", name="body", @SWG\Schema(ref="#/definitions/eventRequest")),
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
    public function add(Request $request, Response $response, $args): Response
    {
        if ($this->tokenWithError($response, $request)) {
            return $response->withStatus(401);
        }

        $userRole = $request->getHeader('userRole')[0];
        $userEmail = $request->getHeader('userEmail')[0];

        if (!in_array($userRole, [AuthorizeMiddleware::LOCAL_ADMIN])) {
            return $response->withStatus(403);
        }

        $rowParams = json_decode($request->getBody()->getContents(), true);
        $rowParams['organizationId'] = (int)$args['id'];

        $errors = (new EventValidator())->validate($rowParams);

        if (count($errors) > 0) {
            return $this->respond(400, ['errors' => $errors], $response);
        }

        $event = new Event('-1', $rowParams['organizationId'], $rowParams['name'], new DateTime($rowParams['startDate']), new DateTime($rowParams['expirationDate']), $rowParams['description'], Event::LEAD_UP);

        $eventId = $this->eventService->add($event, $userEmail, $response);

        if ($eventId instanceof Response) {
            return $eventId;
        }
        return $this->respond(200, ['id' => $eventId], $response);
    }

    /**
     *
     * * @SWG\Delete(
     *   path="/api/v1/organization/{id}/event/{eventId}",
     *   summary="удаляет мероприятие, относящего к определенной организации, по id(локальный админ)",
     *   tags={"Event"},
     *   @SWG\Parameter(in="query", name="id", type="integer", description="id организации"),
     *   @SWG\Parameter(in="query", name="eventId", type="integer", description="id мероприятия"),
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
    public function delete(Request $request, Response $response, $args): Response
    {
        if ($this->tokenWithError($response, $request)) {
            return $response->withStatus(401);
        }

        $userRole = $request->getHeader('userRole')[0];
        $userEmail = $request->getHeader('userEmail')[0];

        if (!in_array($userRole, [AuthorizeMiddleware::LOCAL_ADMIN])) {
            return $response->withStatus(403);
        }

        $result = $this->eventService->delete((int)$args['id'], (int)$args['eventId'], $response, $userEmail);

        if ($result instanceof Response) {
            return $result;
        }

        return $response;
    }

    /**
     *
     * * @SWG\Get(
     *   path="/api/v1/organization/{id}/event/{eventId}",
     *   summary="получение мероприятия по id, относящийся к конекртной организации",
     *   tags={"Event"},
     *   @SWG\Parameter(in="query", name="id", type="integer", description="id организации"),
     *   @SWG\Parameter(in="query", name="eventId", type="integer", description="id мероприятия"),
     *   @SWG\Response(response=200, description="OK",
     *          @SWG\Schema(ref="#/definitions/eventResponse")
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
    public function get(Request $request, Response $response, $args): Response
    {

        $result = $this->eventService->get((int)$args['id'], (int)$args['eventId'], $response);

        if ($result instanceof Response) {
            return $result;
        }

        if ($result == null){
            return $response->withStatus(404);
        }

        return $this->respond(200, $result->toArray(), $response);
    }

    /**
     *
     * * @SWG\Get(
     *   path="/api/v1/organization/{id}/event",
     *   summary="получение всех всех мероприятий, относящихся к определенной организации",
     *   tags={"Event"},
     *   @SWG\Parameter(in="query", name="id", type="integer", description="id организации"),
     *   @SWG\Response(response=200, description="OK",
     *          @SWG\Property(type="array", @SWG\Items(ref="#/definitions/eventResponse"))
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
    public function getAll(Request $request, Response $response, $args): Response
    {
        $results = $this->eventService->getAll((int)$args['id']);

        if ($results instanceof Response) {
            return $results;
        }

        if ($results == null){
            return $this->respond(200, [], $response);
        }

        return $this->respond(200, $results, $response);
    }

    /**
     *
     * @SWG\Put(
     *   path="/api/v1/organization/{id}/event/{eventId}",
     *   summary="обновляет данные о мероприятии, id которого передан(локальный админ или секретарь, который относится к данному мероприятию)",
     *   tags={"Event"},
     *   @SWG\Parameter(in="query", name="id", type="integer", description="id организации"),
     *   @SWG\Parameter(in="query", name="eventId", type="integer", description="id мероприятия"),
     *   @SWG\Parameter(in="header", name="Authorization", type="string", description="токен"),
     *   @SWG\Parameter(in="body", name="body", @SWG\Schema(ref="#/definitions/eventRequest")),
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
    public function update(Request $request, Response $response, $args):Response
    {
        if ($this->tokenWithError($response, $request)) {
            return $response->withStatus(401);
        }

        $userRole = $request->getHeader('userRole')[0];
        $userEmail = $request->getHeader('userEmail')[0];

        $eventId = (int)$args['eventId'];
        $organizationId = (int)$args['id'];
        $access = $this->accessService->hasAccessWorkWithEvent($eventId, $organizationId, $userEmail, $userRole);

        if ($access === false){
            return $response->withStatus(403);
        }else if ($access !== true){
            /**@var $access array*/
            return $this->respond(400, ['errors' => $access], $response);
        }

        $rowParams = json_decode($request->getBody()->getContents(), true);
        $rowParams['organizationId'] = (int)$args['id'];
        $rowParams['eventId'] = (int)$args['eventId'];

        $errors = (new EventValidator())->validate($rowParams);

        if (count($errors) > 0) {
            return $this->respond(400, ['errors' => $errors], $response);
        }

        $event = new Event($rowParams['eventId'], $rowParams['organizationId'], $rowParams['name'], new DateTime($rowParams['startDate']), new DateTime($rowParams['expirationDate']), $rowParams['description']);

        return $this->eventService->update($event, $userEmail, $response);
    }
}