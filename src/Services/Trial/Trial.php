<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 28.11.2019
 * Time: 7:25
 */

namespace App\Services\Trial;
use App\Domain\Models\Trial as TrialModel;
use App\Persistance\Repositories\TrialRepository\TrialRepository;
use Psr\Http\Message\ResponseInterface as Response;
use App\Services\Presenters\TrialToResponsePresenter;

class Trial
{
    private $trialRep;
    public function __construct(TrialRepository $trialRepository)
    {
        $this->trialRep = $trialRepository;
    }

    public function getTrialsByGenderAndAge(array $params, Response $response):Response
    {
        $trials = $this->trialRep->getList($params['gender'], $params['age']);

        $trialsToRespond = [];

        foreach ($trials as $trial)
        {
            $trialsToRespond[] = TrialToResponsePresenter::getView($trial);
        }

        $nameAgeCategory = $this->trialRep->getNameOfAgeCategory($params['age']);
        $response->getBody()->write(json_encode(['trials' => $trialsToRespond, 'ageCategory' => $nameAgeCategory]));
        return $response->withStatus(200);
    }

    public function getSecondResult(array $params, Response $response)
    {
        $params['trialId'] = $params['id'];
        $secondResult = $this->trialRep->getSecondResult($params['firstResult'], $params['trialId']);
        $response->getBody()->write(json_encode(['secondResult' => $secondResult]));
        return $response;
    }
}