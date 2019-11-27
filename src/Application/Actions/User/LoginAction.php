<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 05.11.2019
 * Time: 1:01
 */


namespace App\Application\Actions\User;


use App\Application\Actions\Action;
use App\Application\Actions\ActionError;
use App\Domain\DomainException\DomainRecordNotFoundException;
use App\Persistance\Repositories\User\RefreshToken;
use App\Persistance\Repositories\User\UserRepository;
use App\Services\Logger;
use App\Services\Token\Token;
use App\Services\Validators\ValidatorInterface;
use Illuminate\Support\Facades\Log;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpBadRequestException;
use Symfony\Component\Validator\Constraints\DateTime;

class LoginAction extends Action
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

        $userRep = new UserRepository();

        if (!$userRep->userIsSetOnDB($params['email'], Token::getEncodedPassword($params['password']))){
            $this->response->getBody()->write(json_encode(['errors' => array(new ActionError(ActionError::VERIFICATION_ERROR, 'wrong login or password'))]));
            return $this->response->withStatus(400);
        }
        $role = $userRep->getRoleOfUser($params['email']);

        $refreshToken = Token::getEncodedToken([
            'email' => $params['email'],
            'role' => $role,
            'type' => 'refresh token',
            'liveTime' => 24 * 7 * 3600,
            'addedTime' => (new \DateTime)
                ->setTimezone(new \DateTimeZone('europe/moscow'))
                ->format('Y-m-d H:i:s')
        ]);

        $accessToken = Token::getEncodedToken([
            'email' => $params['email'],
            'role' => $role,
            'type' => 'acess token',
            'liveTime' => 600,
            'addedTime' => (new \DateTime())
                ->setTimezone(new \DateTimeZone('europe/moscow'))
                ->format('Y-m-d H:i:s')
        ]);

        $rToken = new RefreshToken();
        $rToken->deleteRefreshTokenWithEmail($params['email']);
        $rToken->addRefreshToken($refreshToken, $params['email']);

        $this->response->getBody()->write(json_encode([
            'accessToken' => $accessToken,
            'refreshToken' => $refreshToken
        ]));
        return $this->response;
    }
}