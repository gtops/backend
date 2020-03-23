<?php

namespace App\Application\Actions\EventParticipant;

use App\Application\Actions\Action;
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
}