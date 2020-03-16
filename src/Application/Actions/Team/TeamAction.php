<?php

namespace App\Application\Actions\Team;
use App\Application\Actions\Action;
use App\Services\Team\TeamService;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class TeamAction extends Action
{
    private $temaService;
    public function __construct(TeamService $teamService)
    {
        $this->temaService = $teamService;
    }

    /**
     *
     * @SWG\Post(
     *   path="/api/v1/organization/{id}/event/{eventId}/team",
     *   summary="добавляет команду в определенное мероприятие",
     *   tags={"Team"},
     *   @SWG\Parameter(in="header", name="Authorization", type="string", description="токен"),
     *   @SWG\Parameter(in="query", name="id", type="integer", description="id организации"),
     *   @SWG\Parameter(in="query", name="eventId", type="integer", description="id мероприятия"),
     *   @SWG\Parameter(in="body", name="body", @SWG\Schema(@SWG\Property(property="name", type="string"))),
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

    }

    /**
     *
     * @SWG\Get(
     *   path="/api/v1/organization/{id}/event/{eventId}/team/{teamId}",
     *   summary="получает данные определенной команды, относящейся к определенному мероприятию",
     *   tags={"Team"},
     *   @SWG\Parameter(in="header", name="Authorization", type="string", description="токен"),
     *   @SWG\Parameter(in="query", name="id", type="integer", description="id организации"),
     *   @SWG\Parameter(in="query", name="eventId", type="integer", description="id мероприятия"),
     *   @SWG\Parameter(in="query", name="teamId", type="integer", description="id мероприятия"),
     *   @SWG\Response(response=200, description="OK", @SWG\Schema(ref="#/definitions/teamResponse")),
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
    public function get(Request $request, Response $response, $args): Response
    {

    }

    /**
     *
     * @SWG\Get(
     *   path="/api/v1/organization/{id}/event/{eventId}/team",
     *   summary="получает данные команд, относящихся к определенному мероприятию",
     *   tags={"Team"},
     *   @SWG\Parameter(in="header", name="Authorization", type="string", description="токен"),
     *   @SWG\Parameter(in="query", name="id", type="integer", description="id организации"),
     *   @SWG\Parameter(in="query", name="eventId", type="integer", description="id мероприятия"),
     *   @SWG\Response(response=200, description="OK", @SWG\Property(type="array", @SWG\Items(ref="#/definitions/teamResponse")),
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

    }

    /**
     *
     * @SWG\Delete(
     *   path="/api/v1/organization/{id}/event/{eventId}/team/{teamId}",
     *   summary="удаляет определенную команду, относящейся к определенному мероприятию",
     *   tags={"Team"},
     *   @SWG\Parameter(in="header", name="Authorization", type="string", description="токен"),
     *   @SWG\Parameter(in="query", name="id", type="integer", description="id организации"),
     *   @SWG\Parameter(in="query", name="eventId", type="integer", description="id мероприятия"),
     *   @SWG\Parameter(in="query", name="teamId", type="integer", description="id мероприятия"),
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

    }

    /**
     *
     * @SWG\Post(
     *   path="/api/v1/organization/{id}/event/{eventId}/team/{teamId}",
     *   summary="редактирует данные команды",
     *   tags={"Team"},
     *   @SWG\Parameter(in="header", name="Authorization", type="string", description="токен"),
     *   @SWG\Parameter(in="query", name="id", type="integer", description="id организации"),
     *   @SWG\Parameter(in="query", name="eventId", type="integer", description="id мероприятия"),
     *   @SWG\Parameter(in="body", name="body", @SWG\Schema(@SWG\Property(property="name", type="string"))),
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
    public function update(Request $request, Response $response, $args): Response
    {

    }
}