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

class TrialAction extends Action
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