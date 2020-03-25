<?php

namespace App\Application\Actions\TeamLead;

use App\Application\Actions\Action;
use App\Domain\Models\TeamLead\TeamLead;
use App\Services\TeamLead\TeamLeadService;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class TeamLeadAction extends Action
{
    /**
     *
     * @SWG\Post(
     *   path="/api/v1/team/{teamId}/teamLead",
     *   summary="добавляет тренера к команде(секретарь, локальный админ)",
     *   tags={"TeamLead"},
     *   @SWG\Parameter(in="header", name="Authorization", type="string", description="токен"),
     *   @SWG\Parameter(in="query", name="teamId", type="integer", description="id команды"),
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

    private $teamLeadService;

    public function __construct(TeamLeadService $teamLeadService)
    {
        $this->teamLeadService = $teamLeadService;
    }

    public function add()
    {

    }

    /**
     *
     * @SWG\Delete(
     *   path="/api/v1/teamLead/{teamLeadId}",
     *   summary="удаляет тренера от команды(секретарь, локальный админ)",
     *   tags={"TeamLead"},
     *   @SWG\Parameter(in="header", name="Authorization", type="string", description="токен"),
     *   @SWG\Parameter(in="query", name="teamLeadId", type="integer", description="id тренера"),
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

    /**
     *
     * @SWG\Get(
     *   path="/api/v1/team/{teamId}/teamLead",
     *   summary="получает список всех тренеров, относящихся к команде(все)",
     *   tags={"TeamLead"},
     *   @SWG\Parameter(in="header", name="Authorization", type="string", description="токен"),
     *   @SWG\Parameter(in="query", name="teamId", type="integer", description="id команды"),
     *   @SWG\Response(response=200, description="OK", @SWG\Property(type="array", @SWG\Items(ref="#/definitions/teamLead")),
     *  @SWG\Response(response=400, description="Error", @SWG\Schema(
     *          @SWG\Property(property="errors", type="array", @SWG\Items(
     *              @SWG\Property(property="type", type="string"),
     *              @SWG\Property(property="description", type="string")
     *          ))
     *     )))
     * )
     *
     */

    public function getAllForTeam(Request $request, Response $response, $args):Response
    {
        $teamId = (int)$args['teamId'];
        $teamLeads = $this->teamLeadService->getAllForTeam($teamId);
        $teamLeadsInArray = [];
        foreach ($teamLeads as $teamLead){
            $teamLeadsInArray[] = $teamLead->toArray();
        }

        return $this->respond(200, $teamLeadsInArray, $response);
    }
}