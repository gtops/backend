<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 04.11.2019
 * Time: 19:49
 */

namespace App\Application\Actions\User;


use App\Application\Actions\Action;
use App\Application\Actions\ActionError;
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

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @return Response
     * @throws DomainRecordNotFoundException
     * @throws HttpBadRequestException
     */
    protected function action(): Response
    {
        $params = json_decode($this->request->getBody()->getContents(), true);
        $errors = $this->validator->getErrors($params);

        if (count($errors) > 0){
            $this->response->getBody()->write(json_encode(array('errors' => $errors)));
            return $this->response->withStatus(400);
        }

        $regTokenRep = new RegistrationToken();
        $regTokenRep->deleteTokenFromDB($params['token']);

        $jwtData = (array)Token::getDecodedToken($params['token']);
        $userRep = new UserRepository();
        $params['password'] = Token::getEncodedPassword($params['password']);

        if ($userRep->userIsSetOnDBWithEmail($jwtData['email'])){
            $this->response->getBody()->write(json_encode(['errors' => array(new ActionError(ActionError::BAD_REQUEST, 'this email isset'))]));
            return $this->response->withStatus(400);
        }

        $userRep->createUser([
            'email' => $jwtData['email'],
            'role' => $jwtData['role'],
            'password' => $params['password'],
            'name' => $params['name']
        ]);

        return $this->response->withStatus(200);
    }
}