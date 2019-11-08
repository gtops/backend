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
use App\Persistance\Repositories\User\RegistrationToken;

/**
 *
 * * @SWG\Post(
 *   path="/organization/invite",
 *   summary="Отправка приглашения на регистрацию",
 *   operationId="Отправка приглашения на регистрацию",
 *   tags={"Invite"},
 *   @SWG\Parameter(in="body", name="body", @SWG\Schema(
 *      @SWG\Property(property="email", type="string"),
 *      @SWG\Property(property="role", type="string"),
 *      @SWG\Property(property="token", type="string")
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
        $params = json_decode($this->request->getBody()->getContents(), true);
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
            'type' => 'access token'
        ]);

        $regToken = new RegistrationToken();
        $regToken->addTokenToDB($token);

        try {
            $this->emailSendler->sendInvite($params['email'], $token);
        }catch (\Exception $err){
            $this->response->getBody()->write(json_encode(new ActionError(ActionError::BAD_REQUEST, 'invalid email')));
        }

        return $this->response;
    }
}