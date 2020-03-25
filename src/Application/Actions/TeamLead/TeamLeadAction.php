<?php

namespace App\Application\Actions\TeamLead;

class TeamLeadAction
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
    public function getAll()
    {

    }
}