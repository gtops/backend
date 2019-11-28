<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 03.11.2019
 * Time: 23:13
 */

namespace App\Application\Actions\User;


use App\Application\Actions\Action;
use App\Application\Actions\ActionError;
use App\Domain\DomainException\DomainRecordNotFoundException;
use App\Services\EmailSendler\EmailSendler;
use App\Services\Token\Token;
use App\Services\Validators\ValidatorInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpBadRequestException;
use App\Persistance\Repositories\User\RegistrationTokenRepository;

/**
 *
 * * @SWG\Post(
 *   path="/organization/invite",
 *   summary="Отправка приглашения на регистрацию",
 *   operationId="Отправка приглашения на регистрацию",
 *   tags={"Invite"},
 *   @SWG\Parameter(in="header", name="Authorization", type="string"),
 *   @SWG\Parameter(in="body", name="body", @SWG\Schema(
 *      @SWG\Property(property="email", type="string"),
 *      @SWG\Property(property="role", type="string")
 *    )),
 *   @SWG\Response(response=200, description="OK"),
 *  @SWG\Response(response=400, description="Error", @SWG\Schema(
 *          @SWG\Property(property="errors", type="array", @SWG\Items(
 *              @SWG\Property(property="type", type="string"),
 *              @SWG\Property(property="description", type="string")
 *          ))
 *     ))
 * )
 *
 */

class SendInviteAction extends Action
{

    /**
     * @return Response
     * @throws DomainRecordNotFoundException
     * @throws HttpBadRequestException
     */

    private $emailSendler;
    private $token;
    private $validator;

    public function __construct(EmailSendler $emailSendler, ValidatorInterface $validator)
    {
        $this->emailSendler = $emailSendler;
        $this->validator = $validator;
    }

    protected function action(): Response
    {
        if(!isset($this->request->getHeader('Authorization')[0])){
            $this->response->getBody()->write(json_encode(new ActionError(ActionError::VALIDATION_ERROR, 'not all parameters passed')));
            return $this->response->withStatus(400);
        }

        $params = json_decode($this->request->getBody()->getContents(), true);
        $params['token'] = $this->request->getHeader('Authorization')[0];
        $errors = $this->validator->getErrors($params);

        if (count($errors) > 0){
            $this->response->getBody()->write(json_encode(array('errors' => $errors)));
            return $this->response->withStatus(400);
        }

        $role = $params['role'];
        $email = $params['email'];

        $token = Token::getEncodedToken([
            'email' => $email,
            'role' => $role,
            'type' => 'access token',
            'liveTime' => 24 * 7 * 3600,
            'addedTime' => (new \DateTime)
                ->setTimezone(new \DateTimeZone('europe/moscow'))
                ->format('Y-m-d H:i:s')
        ]);

        $regToken = new RegistrationTokenRepository();
        $regToken->addTokenToDB($token);

        try {
            $this->emailSendler->sendInvite($params['email'], $token);
        }catch (\Exception $err){
            $this->response->getBody()->write(json_encode(new ActionError(ActionError::BAD_REQUEST, 'invalid email')));
            return $this->response->withStatus(400);
        }

        return $this->response;
    }
}