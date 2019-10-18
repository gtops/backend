<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 18.10.2019
 * Time: 2:45
 */

namespace App\Application\Actions\Trial;


use App\Application\Actions\Action;
use Monolog\Logger;
use Psr\Http\Message\ResponseInterface as Response;
use Illuminate\Database\Capsule\Manager as Capsule;
use App\Persistance\Repositories\TrialRepository\TrialRepository;

/**
 *
 * * @SWG\Get(
 *   path="trial/result",
 *   summary="Получение вторичного результата по испытанию исходя из первичного результата из таблицы по переводу",
 *   operationId="Получение вторичного результата по испытанию исходя из первичного результата из таблицы по переводу",
 *   tags={"Trial"},
 *   @SWG\Parameter(in="query", name="firstResult", type="integer", required=true),
 *   @SWG\Parameter(in="query", name="trialId", type="integer", required=true),
 *   @SWG\Response(response=200, description="OK", @SWG\Schema(
 *          @SWG\Property(property="statusCode", type="integer"),
 *          @SWG\Property(property="data", type="array", @SWG\Items(
 *              @SWG\Property(property="secondResult", type="number")
 *          )
 *     )))
 * )
 *
 */

class GetSecondResultOfTrialByFirstResultAction extends Action
{
    protected function action(): Response
    {
        $trialRep = new TrialRepository();
        $params = $this->request->getQueryParams();

        $secondResult = $trialRep->getSecondResult($params['firstResult'], $params['trialId']);

        return $this->respondWithData([
            'secondResult' => $secondResult
        ]);
    }
}