<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 08.11.2019
 * Time: 3:41
 */


namespace App\Application\Actions\User;


use App\Application\Actions\Action;
use App\Application\Actions\ActionError;
use App\Domain\DomainException\DomainRecordNotFoundException;
use App\Persistance\Repositories\User\RefreshToken;
use App\Services\Logger;
use App\Services\Token\Token;
use App\Services\Validators\ValidatorInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpBadRequestException;

class GetNewTokensAction extends Action
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
        if(!isset($this->request->getHeader('refreshToken')[0])){
            $this->response->getBody()->write(json_encode(new ActionError(ActionError::VALIDATION_ERROR, 'not all parameters passed')));
            return $this->response->withStatus(400);
        }

        $params = [
            'refreshToken' => $this->request->getHeader('refreshToken')[0]
        ];
        $errors = $this->validator->getErrors($params);

        if (count($errors) > 0){
            $this->response->getBody()->write(json_encode(array('errors' => $errors)));
            return $this->response->withStatus(400);
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

        $rToken = new RefreshToken();
        $rToken->updateRefreshTokenWithEmail($decodedToken['email'], $refreshToken);

        $this->response->getBody()->write(json_encode([
            'accessToken' => $accessToken,
            'refreshToken' => $refreshToken
        ]));
        return $this->response;
    }
}