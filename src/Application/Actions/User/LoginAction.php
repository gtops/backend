<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 05.11.2019
 * Time: 1:01
 */

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

        $logger = new \Monolog\Logger('a');
        $logger->alert((new \DateTime())->setTimezone(new \DateTimeZone('europe/moscow'))->format('Y-m-d H:i:s'));

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
            'liveTime' => 120,
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