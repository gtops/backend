<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 27.11.2019
 * Time: 2:49
 */

namespace App\Services\Auth;


use App\Application\Actions\ActionError;
use App\Persistance\Repositories\User\RefreshToken;
use App\Persistance\Repositories\User\UserRepository;
use App\Services\Token\Token;
use Composer\Repository\RepositoryInterface;
use Psr\Http\Message\ResponseInterface as Response;

class Auth
{
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function login(array $params, Response $response):Response
    {
        if (!$this->userRepository->userIsSetOnDB($params['email'], Token::getEncodedPassword($params['password']))){
            $response->getBody()->write(json_encode(['errors' => array(new ActionError(ActionError::VERIFICATION_ERROR, 'wrong login or password'))]));
            return $response->withStatus(400);
        }
        $role = $this->userRepository->getRoleOfUser($params['email']);

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

        $response->getBody()->write(json_encode([
            'accessToken' => $accessToken,
            'refreshToken' => $refreshToken
        ]));

        return $response;
    }
}