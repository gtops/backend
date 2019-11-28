<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 28.11.2019
 * Time: 6:49
 */

namespace App\Application\Actions\Trial;


use App\Application\Actions\Action;
use App\Services\Trial\Trial;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\RequestInterface as Request;
use Symfony\Component\Validator\Constraints as Assert;

class TrialAction extends Action
{
    private $trialService;

    public function __construct(Trial $trial)
    {
        $this->trialService = $trial;
        parent::__construct();
    }

    public function getTrialsByGenderAndAge(Request $request, Response $response, $args): Response
    {
        $constraints = new Assert\Collection([
            'gender' => [
                new Assert\Choice([
                    'choices' => [0, 1],
                    'message' => 'пол либо мужской - 1, либо женский - 0'
                ]),
                new Assert\NotNull(['message' => 'поле gender не может быть пустым'])
            ],
            'age' => [
                new Assert\NotNull(['message' => 'поле age не может быть пустым']),
                new Assert\GreaterThan([
                    'value' => 7,
                    'message' => 'минимальный возраст для соревнования 8 лет'
                ])
            ],
        ]);
        $params = [
            'gender' => (int)$args['gender'],
            'age' => (int)$args['age']
        ];

        $errors = $this->getErrors($constraints, $params);

        if (count($errors) > 0){
            return $this->respond(400, ['errros' => $errors], $response);
        }

        return $this->trialService->getTrialsByGenderAndAge($params, $response);
    }

    public function getSecondResult(Request $request, Response $response, $args): Response
    {
        $constraints = new Assert\Collection([
            'firstResult' => [
                new Assert\NotNull(['message' => 'поле firstResult не может быть пустым'])
            ],
            'id' => [
                new Assert\NotNull(['message' => 'поле trialId не может быть пустым']),
                new Assert\GreaterThan([
                    'value' => 0,
                    'message' => 'минимальный trialId для соревнования 1'
                ])
            ],
        ]);
        $params = [
            'firstResult' => (int)$args['firstResult'],
            'id' => (int)$args['id']
        ];

        $errors = $this->getErrors($constraints, $params);

        if (count($errors) > 0){
            return $this->respond(400, ['errros' => $errors], $response);
        }

        return $this->trialService->getSecondResult($params, $response);
    }
}