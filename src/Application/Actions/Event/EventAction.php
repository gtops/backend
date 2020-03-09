<?php

namespace App\Application\Actions\Event;
use App\Application\Actions\Action;
use App\Application\Middleware\AuthorizeMiddleware;
use App\Domain\Models\Event\Event;
use App\Services\Event\EventService;
use App\Validators\Event\EventValidator;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class EventAction extends Action
{
    private $eventService;

    public function __construct(EventService $eventService)
    {
        $this->eventService = $eventService;
    }

    /**
     *
     * @SWG\Post(
     *   path="/api/v1/organization/{id}/event",
     *   summary="добавляет мероприятие к организации локальным админом",
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

        $event = new Event('-1', $rowParams['organizationId'], $rowParams['name'], new \DateTime($rowParams['startDate']), new \DateTime($rowParams['expirationDate']), $rowParams['description']);

        $eventId = $this->eventService->add($event, $userEmail, $response);

        if ($eventId instanceof ResponseInterface) {
            return $eventId;
        }
        return $this->respond(200, ['id' => $eventId], $response);
    }

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

    public function get(Request $request, Response $response, $args): Response
    {
        if ($this->tokenWithError($response, $request)) {
            return $response->withStatus(401);
        }

        $userRole = $request->getHeader('userRole')[0];
        $userEmail = $request->getHeader('userEmail')[0];

        $result = $this->eventService->get((int)$args['id'], (int)$args['eventId'], $response);

        if ($result instanceof Response) {
            return $result;
        }

        return $this->respond(200, $result->toArray(), $response);
    }

    public function getAll(Request $request, Response $response, $args): Response
    {
        if ($this->tokenWithError($response, $request)) {
            return $response->withStatus(401);
        }

        $userRole = $request->getHeader('userRole')[0];
        $userEmail = $request->getHeader('userEmail')[0];
    }

    public function update(Request $request, Response $response, $args):Response
    {
        if ($this->tokenWithError($response, $request)) {
            return $response->withStatus(401);
        }
    }
}