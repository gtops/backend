<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 25.11.2019
 * Time: 1:24
 */

namespace App\Application\Actions\User;

use Symfony\Component\Validator\Constraints as Assert;
use App\Services\Logger;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Application\Actions\Action;
use \App\Services\Auth\Auth as AuthService;

class Auth extends Action
{
    private $auth;

    public function __construct(AuthService $auth)
    {
        parent::__construct();
        $this->auth = $auth;
    }

    /**
     *
     * * @SWG\Post(
     *   path="/registration",
     *   summary="регистрирует пользователя по приглашению",
     *   operationId="регистрирует пользователя по приглашению",
     *   tags={"User"},
     *   @SWG\Parameter(in="body", name="body", @SWG\Schema(
     *      @SWG\Property(property="token", type="string"),
     *      @SWG\Property(property="name", type="string"),
     *      @SWG\Property(property="password", type="string", description="length min 6 symbols")
     *    )),
     *   @SWG\Response(response=200, description="OK"),
     *   @SWG\Response(response=400, description="Error", @SWG\Schema(
     *          @SWG\Property(property="errors", type="array", @SWG\Items(
     *              @SWG\Property(property="type", type="string"),
     *              @SWG\Property(property="description", type="string")
     *          ))
     *     ))
     * )
     *
     */

    public function registration(Request $request, Response $response, $args): Response
    {
        return $response;
    }

    /**
     *
     * * @SWG\Post(
     *   path="/login",
     *   summary="авторизует пользователей, возвращая аксесс и рефреш токены",
     *   operationId="авторизует пользователей, возвращая аксесс и рефреш токены",
     *   tags={"User"},
     *   @SWG\Parameter(in="body", name="body", @SWG\Schema(
     *      @SWG\Property(property="email", type="string"),
     *      @SWG\Property(property="password", type="string", description="length min 6 symbols")
     *    )),
     *   @SWG\Response(response=200, description="OK", @SWG\Schema(
     *              @SWG\Property(property="accessToken", type="string"),
     *              @SWG\Property(property="refreshToken", type="string")
     *          )),
     *   @SWG\Response(response=400, description="Error", @SWG\Schema(
     *          @SWG\Property(property="errors", type="array", @SWG\Items(
     *              @SWG\Property(property="type", type="string"),
     *              @SWG\Property(property="description", type="string")
     *          ))
     *     ))
     * )
     *
     */


    public function login(Request $request, Response $response, $args): Response
    {
        $constraints = new Assert\Collection([
            'password' => [new Assert\Length(['min' => 2]), new Assert\NotBlank],
            'email' => [new Assert\Email(), new Assert\notBlank],
        ]);

        $params = json_decode($request->getBody()->getContents(), true);
        $errors = $this->getErrors($constraints, $params);

        if (count($errors) > 0){
            return $this->respond(400, ['errors' => $errors], $response);
        }

        return $this->auth->login($params, $response);
    }

    /**
     *
     * * @SWG\Post(
     *   path="/token/refresh",
     *   summary="возвращает новую пару аксесс и рефреш токенов",
     *   operationId="возвращает новую пару аксесс и рефреш токенов",
     *   tags={"User"},
     *   @SWG\Parameter(in="header", name="refreshToken", type="string"),
     *   @SWG\Response(response=200, description="OK", @SWG\Schema(
     *              @SWG\Property(property="accessToken", type="string"),
     *              @SWG\Property(property="refreshToken", type="string")
     *          )),
     *   @SWG\Response(response=400, description="Error", @SWG\Schema(
     *          @SWG\Property(property="errors", type="array", @SWG\Items(
     *              @SWG\Property(property="type", type="string"),
     *              @SWG\Property(property="description", type="string")
     *          ))
     *     ))
     * )
     *
     */

    public function refresh(Request $request, Response $response, $args): Response
    {
        return $response;
    }
}