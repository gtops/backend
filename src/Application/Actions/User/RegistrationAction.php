<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 04.11.2019
 * Time: 19:49
 */

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

namespace App\Application\Actions\User;


use App\Application\Actions\Action;
use App\Domain\DomainException\DomainRecordNotFoundException;
use App\Persistance\ModelsEloquant\User\User;
use App\Persistance\Repositories\User\RegistrationToken;
use App\Services\Token\Token;
use App\Services\Validators\ValidatorInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpBadRequestException;
use App\Persistance\Repositories\User\UserRepository;

class RegistrationAction extends Action
{
    private $validator;
    private $tokenHandler;

    public function __construct(Token $tokenHandler, ValidatorInterface $validator)
    {
        $this->validator = $validator;
        $this->tokenHandler = $tokenHandler;
    }

    /**
     * @return Response
     * @throws DomainRecordNotFoundException
     * @throws HttpBadRequestException
     */
    protected function action(): Response
    {
        $params = json_decode($this->request->getBody()->getContents(), true);
        $errors = $this->validator->getErrors($params, ['tokenHandler' => $this->tokenHandler]);

        if (count($errors) > 0){
            $this->response->getBody()->write(json_encode(array('errors' => $errors)));
            return $this->response->withStatus(400);
        }

        $regTokenRep = new RegistrationToken();
        $regTokenRep->deleteTokenFromDB($params['token']);

        $jwtData = (array)$this->tokenHandler->getDecodedToken($params['token'], 3600*24);
        $userRep = new UserRepository();
        $userRep->createUser([
            'email' => $jwtData['email'],
            'role' => $jwtData['role'],
            'password' => $params['password'],
            'name' => $params['name']
        ]);

        return $this->response->withStatus(200);
    }
}