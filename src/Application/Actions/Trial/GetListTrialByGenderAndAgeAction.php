<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 17.10.2019
 * Time: 23:39
 */

namespace App\Application\Actions\Trial;


use App\Application\Actions\Action;
use App\Domain\Models\Trial;
use Psr\Http\Message\ResponseInterface as Response;
use Illuminate\Database\Capsule\Manager as Capsule;
use App\Persistance\Repositories\TrialRepository\TrialRepository;
use App\Services\Presenters\TrialToResponsePresenter;

/**
 *
 * * @SWG\Get(
 *   path="trial",
 *   summary="получение списка испытаний для определенного пола и возраста",
 *   operationId="получение списка испытаний для определенного пола и возраста",
 *   tags={"Trial"},
 *   @SWG\Parameter(in="query", name="gender", type="integer", required=true, description="gender: 0 - female, 1 - male", required=true),
 *   @SWG\Parameter(in="query", name="age", type="integer", required=true),
 *   @SWG\Response(response=200, description="OK", @SWG\Schema(
 *          @SWG\Property(property="statusCode", type="integer"),
 *          @SWG\Property(property="data", type="array", @SWG\Items(
 *              @SWG\Property(property="trialName", type="string"),
 *              @SWG\Property(property="trialId", type="integer"),
 *              @SWG\Property(property="resultForBronze", type="number"),
 *              @SWG\Property(property="resultForSilver", type="number"),
 *              @SWG\Property(property="resultForGold", type="number")
 *          )
 *     )))
 * )
 *
 */

class GetListTrialByGenderAndAgeAction extends Action
{
    private $capsule;

    public function __construct(Capsule $capsule)
    {
        $this->capsule = $capsule;
    }

    protected function action(): Response
    {
        $trialRep = new TrialRepository();
        $params = $this->request->getQueryParams();
        $trials = $trialRep->getList($params['gender'], $params['age'], $this->capsule);

        $trialsToRespond = [];

        foreach ($trials as $trial)
        {
            $trialsToRespond[] = $this->getTrialForResponse($trial);
        }

        return $this->respondWithData($trialsToRespond);

    }

    private function getTrialForResponse(Trial $trial):array
    {
        $trialPresenter = new TrialToResponsePresenter($trial);
        return $trialPresenter->getView();
    }
}