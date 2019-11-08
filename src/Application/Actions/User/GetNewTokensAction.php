<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 08.11.2019
 * Time: 3:41
 */

/**
 *
 * * @SWG\Post(
 *   path="/token/refresh",
 *   summary="возвращает новую пару аксесс и рефреш токенов",
 *   operationId="возвращает новую пару аксесс и рефреш токенов",
 *   tags={"User"},
 *   @SWG\Parameter(in="body", name="body", @SWG\Schema(
 *      @SWG\Property(property="refreshToken", type="string")
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

namespace App\Application\Actions\User;


use App\Application\Actions\Action;
use App\Domain\DomainException\DomainRecordNotFoundException;
use App\Persistance\Repositories\User\RefreshToken;
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
        $params = json_decode($this->request->getBody()->getContents(), true);
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