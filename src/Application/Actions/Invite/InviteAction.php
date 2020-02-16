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
use App\Validators\Invite\InviteValidator;
use App\Validators\ValidateStrategy;
use Monolog\Logger;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Symfony\Component\Validator\Constraints as Assert;

class InviteAction extends Action
{
    private $inviteService;
    private $validator;

    public function __construct(Invite $invite)
    {
        $this->inviteService = $invite;
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

        $validator = new InviteValidator();
        $errors = $validator->validate($params);

        if (count($errors) > 0){
            return $this->respond(400, ['errros' => $errors], $response);
        }

        $response = $response->withHeader('Access-Control-Allow-Headers', 'Authorization');
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

        $response = $response->withHeader('Access-Control-Allow-Headers', 'Authorization');
        return $this->inviteService->valid($params, $response);
    }
}