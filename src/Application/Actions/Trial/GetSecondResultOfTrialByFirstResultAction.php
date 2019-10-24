<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 18.10.2019
 * Time: 2:45
 */

namespace App\Application\Actions\Trial;


use App\Application\Actions\Action;
use App\Services\Validators\ValidatorInterface;
use Monolog\Logger;
use Psr\Http\Message\ResponseInterface as Response;
use Illuminate\Database\Capsule\Manager as Capsule;
use App\Persistance\Repositories\TrialRepository\TrialRepository;

/**
 *
 * * @SWG\Get(
 *   path="/trial/result",
 *   summary="Получение вторичного результата по испытанию исходя из первичного результата из таблицы по переводу",
 *   operationId="Получение вторичного результата по испытанию исходя из первичного результата из таблицы по переводу",
 *   tags={"Trial"},
 *   @SWG\Parameter(in="query", name="firstResult", type="integer", required=true),
 *   @SWG\Parameter(in="query", name="trialId", type="integer", required=true),
 *   @SWG\Response(response=200, description="OK", @SWG\Schema(
 *          @SWG\Property(property="secondResult", type="number")
 *     )),
 *  @SWG\Response(response=400, description="Error", @SWG\Schema(
 *          @SWG\Property(property="errors", type="array", @SWG\Items(
 *              @SWG\Property(property="type", type="string"),
 *              @SWG\Property(property="description", type="string")
 *          ))
 *     )))
 * )
 *
 */

class GetSecondResultOfTrialByFirstResultAction extends Action
{
    private $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    protected function action(): Response
    {
        $trialRep = new TrialRepository();
        $params = $this->request->getQueryParams();

        $errors = $this->validator->getErrors($params);

        if (count($errors) > 0){
            $this->response->getBody()->write(json_encode(array('errors' => $errors)));
            return $this->response->withStatus(400);
        }

        $secondResult = $trialRep->getSecondResult($params['firstResult'], $params['trialId']);
        $this->response->getBody()->write(json_encode(['secondResult' => $secondResult]));
        return $this->response;
    }
}