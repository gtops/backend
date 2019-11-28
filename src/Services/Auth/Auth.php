<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 27.11.2019
 * Time: 2:49
 */

namespace App\Services\Auth;


use App\Application\Actions\ActionError;
use App\Persistance\Repositories\User\RefreshTokenRepository;
use App\Persistance\Repositories\User\RegistrationTokenRepository;
use App\Persistance\Repositories\User\UserRepository;
use App\Services\Token\Token;
use Monolog\Logger;
use Psr\Http\Message\ResponseInterface as Response;

class Auth
{
    private $userRepository;
    private $refTokenRep;
    private $regTokenRep;

    public function __construct(UserRepository $userRepository, RefreshTokenRepository $rToken, RegistrationTokenRepository $regToken)
    {
        $this->userRepository = $userRepository;
        $this->refTokenRep = $rToken;
        $this->regTokenRep = $regToken;
    }

    public function login(array $params, Response $response):Response
    {
        if (!$this->userRepository->userIsSetOnDB($params['email'], Token::getEncodedPassword($params['password']))){
            $response->getBody()->write(json_encode(['errors' => array(new ActionError(ActionError::VERIFICATION_ERROR, 'wrong login or password'))]));
            return $response->withStatus(400);
        }
        $role = $this->userRepository->getRoleOfUser($params['email']);
        $logger = new Logger('a');
        $logger->alert($role);
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

        $rToken = new RefreshTokenRepository();
        $rToken->deleteRefreshTokenWithEmail($params['email']);
        $rToken->addRefreshToken($refreshToken, $params['email']);

        $response->getBody()->write(json_encode([
            'accessToken' => $accessToken,
            'refreshToken' => $refreshToken
        ]));

        return $response;
    }

    public function refresh(array $params, Response $response):Response
    {

        if(!$this->refTokenRep->refreshTokenIsSet($params['refreshToken'])){
            $errors[] = new ActionError(ActionError::VALIDATION_ERROR, 'Такого токена не существует');
            $response->getBody()->write(json_encode(['errors' => $errors]));
            return $response;
        }

        $decodedToken = (array)Token::getDecodedToken($params['refreshToken']);

        $refreshToken = Token::getEncodedToken([
            'email' => $decodedToken['email'],
            'role' => $decodedToken['role'],
            'type' => 'refresh token',
            'liveTime' => 24 * 7 * 3600,
            'addedTime' => (new \DateTime)
                ->setTimezone(new \DateTimeZone('europe/moscow'))
                ->format('Y-m-d H:i:s')
        ]);

        $accessToken = Token::getEncodedToken([
            'email' => $decodedToken['email'],
            'role' => $decodedToken['role'],
            'type' => 'acess token',
            'liveTime' => 120,
            'addedTime' => (new \DateTime())
                ->setTimezone(new \DateTimeZone('europe/moscow'))
                ->format('Y-m-d H:i:s')
        ]);

        $this->refTokenRep->updateRefreshTokenWithEmail($decodedToken['email'], $refreshToken);

        $response->getBody()->write(json_encode([
            'accessToken' => $accessToken,
            'refreshToken' => $refreshToken
        ]));

        return $response;
    }

    public function registration(array $params, Response $response):Response
    {
        $tokenDataFromDb = $this->regTokenRep->getTokenFromDB($params['token']);
        if (!isset($tokenDataFromDb[0]->token)){
            $response->getBody()->write(json_encode(['errors' => array(new ActionError(ActionError::BAD_REQUEST, 'Невалидный токен'))]));
            return $response->withStatus(400);
        }

        $this->regTokenRep->deleteTokenFromDB($params['token']);
        $jwtData = (array)Token::getDecodedToken($params['token']);
        $params['password'] = Token::getEncodedPassword($params['password']);

        if ($this->userRepository->userIsSetOnDBWithEmail($jwtData['email'])){
            $response->getBody()->write(json_encode(['errors' => array(new ActionError(ActionError::BAD_REQUEST, 'такой email существует'))]));
            return $response->withStatus(400);
        }

        $this->userRepository->createUser([
            'email' => $jwtData['email'],
            'role' => $jwtData['role'],
            'password' => $params['password'],
            'name' => $params['name']
        ]);

        return $response->withStatus(200);
    }
}