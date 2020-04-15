<?php

namespace App\Application\Actions\Result;
use App\Application\Actions\Action;
use App\Services\Result\ResultService;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class ResultAction extends Action
{
    private $resultService;
    public function __construct(ResultService $resultService)
    {
        $this->resultService = $resultService;
    }


    /**
     *
     * @SWG\Get(
     *   path="/api/v1/event/{eventId}/user/{userId}/result",
     *   summary="получает результаты для определенного участника",
     *   tags={"Result"},
     *   @SWG\Parameter(in="header", name="Authorization", type="string", description="токен"),
     *   @SWG\Parameter(in="query", name="eventId", type="integer", description="id мероприятия"),
     *     @SWG\Parameter(in="query", name="userId", type="integer", description="id пользователя"),
     *   @SWG\Response(response=200, description="OK", @SWG\Schema(ref="#/definitions/resultForUser")),
     *  @SWG\Response(response=400, description="Error", @SWG\Schema(
     *          @SWG\Property(property="errors", type="array", @SWG\Items(
     *              @SWG\Property(property="type", type="string"),
     *              @SWG\Property(property="description", type="string")
     *          ))
     *     )))
     * )
     */
    public function getResultsOfUserInEvent(Request $request, Response $response, $args): Response
    {
        $eventId = (int)$args['eventId'];
        $userId = (int)$args['userId'];
        $result = $this->resultService->getResultsUfUserInEvent($eventId, $userId);

        if ($result == []){
            return $response->withStatus(404);
        }

        return $this->respond(200, ['groups' => $result['groups'], 'ageCategory' => $result['ageCategory'], 'badge' => $result['badge'], 'countTestsForBronze' => $result['countTestsForBronze'], 'countTestForSilver' => $result['countTestForSilver'], 'countTestsForGold' => $result['countTestsForGold']],  $response);
    }

    /**
     *
     * @SWG\Get(
     *   path="/api/v1/trialInEvent/{trialInEventId}/result",
     *   summary="получает результаты для пользователей определенного вида спорта в мероприятии",
     *   tags={"Result"},
     *   @SWG\Parameter(in="header", name="Authorization", type="string", description="токен"),
     *   @SWG\Parameter(in="query", name="trialInEventId", type="integer", description="id испытания в мероприятии"),
     *   @SWG\Response(response=200, description="OK", @SWG\Property(type="array", @SWG\Items(ref="#/definitions/participantsInTrial")),
     *  @SWG\Response(response=400, description="Error", @SWG\Schema(
     *          @SWG\Property(property="errors", type="array", @SWG\Items(
     *              @SWG\Property(property="type", type="string"),
     *              @SWG\Property(property="description", type="string")
     *          ))
     *     )))
     * )
     */
    public function getResultsOnTrialInEvent(Request $request, Response $response, $args): Response
    {
        $trialInEventId = (int)$args['trialInEventId'];
        $result = $this->resultService->getResultsForTrial($trialInEventId);
        return $this->respond(200, $result, $response);
    }
}