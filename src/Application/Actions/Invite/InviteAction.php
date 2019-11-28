<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 28.11.2019
 * Time: 10:21
 */

namespace App\Application\Actions\Invite;


use App\Application\Actions\Action;
use App\Application\Actions\ActionError;
use App\Services\Invite\Invite;
use Monolog\Logger;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Symfony\Component\Validator\Constraints as Assert;

class InviteAction extends Action
{
    private $inviteService;
    public function __construct(Invite $invite)
    {
        $this->inviteService = $invite;
        parent::__construct();
    }

    /**
     *
     * * @SWG\Post(
     *   path="/api/v1/invite",
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

    public function sendInviteToOrganization(Request $request, Response $response):Response
    {
        if (isset($request->getHeader('error')[0])){
            return $this->respond(403, ['errors' => array(new ActionError(ActionError::UNAUTHENTICATED, $request->getHeader('error')[0]))], $response);
        }

        $params = json_decode($request->getBody()->getContents(), true);
        $params['userEmail'] = $request->getHeader('userEmail')[0];
        $params['userRole'] = $request->getHeader('userRole')[0];

        $constraints = new Assert\Collection([
            'userRole' => [
                new Assert\Choice([
                    'choices' => ['Глобальный администратор'],
                    'message' => 'нет доступа'
                ]),
                new Assert\NotNull(['message' => 'невалидный токен'])
            ],
            'userEmail' => [
                new Assert\NotNull(['message' => 'невалидный токен']),
            ],
            'email' => [
                new Assert\Email([
                   'message' => 'введенный вами email невалиден'
                ]),
                new Assert\NotNull(['message' => 'поле email не может быть пустым не может быть пустым']),
            ],
            'role' => [
                new Assert\NotNull(['message' => 'поле role не может быть пустым']),
            ],
        ]);

        $errors = $this->getErrors($constraints, $params);

        if (count($errors) > 0){
            return $this->respond(400, ['errros' => $errors], $response);
        }

        return $this->inviteService->sendInviteToOrganization($params, $response);
    }

    /**
     *
     * * @SWG\Post(
     *   path="/api/v1/invite/isValid",
     *   summary="проверка валиданости токена приглашеня на регистрацию",
     *   operationId="проверка валиданости токена приглашеня на регистрацию",
     *   tags={"Invite"},
     *   @SWG\Parameter(in="header", name="Authorization", type="string"),
     *   @SWG\Response(response=200, description="OK", @SWG\Schema(
     *              @SWG\Property(property="email", type="string")
     *          )),
     *  @SWG\Response(response=404, description="Not Found")
     *     ))
     * )
     *
     */
    public function validate(Request $request, Response $response):Response
    {
        if (isset($request->getHeader('error')[0])){
            return $this->respond(403, ['errors' => array(new ActionError(ActionError::UNAUTHENTICATED, $request->getHeader('error')[0]))], $response);
        }

        $params['token'] = $request->getHeader('Authorization')[0];
        $params['email'] = $request->getHeader('userEmail')[0];

        return $this->inviteService->valid($params, $response);
    }
}