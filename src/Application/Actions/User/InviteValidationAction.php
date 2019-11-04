<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 04.11.2019
 * Time: 18:17
 */

/**
 *
 * * @SWG\Post(
 *   path="/invite/isValid",
 *   summary="проверка валиданости токена приглашеня на регистрацию",
 *   operationId="проверка валиданости токена приглашеня на регистрацию",
 *   tags={"Invite"},
 *   @SWG\Parameter(in="body", name="body", @SWG\Schema(
 *      @SWG\Property(property="token", type="string")
 *    )),
 *   @SWG\Response(response=200, description="OK"),
 *  @SWG\Response(response=404, description="Not Found")
 *     ))
 * )
 *
 */

namespace App\Application\Actions\User;


use App\Application\Actions\Action;
use App\Domain\DomainException\DomainRecordNotFoundException;
use App\Persistance\Repositories\User\RegistrationToken;
use App\Services\Token\Token;
use Monolog\Logger;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpBadRequestException;

class InviteValidationAction extends Action
{
    private $token;

    public function __construct(Token $token)
    {
        $this->token = $token;
    }

    /**
     * @return Response
     * @throws DomainRecordNotFoundException
     * @throws HttpBadRequestException
     */
    protected function action(): Response
    {
        $tokRep = new RegistrationToken();

        try{
            $params = json_decode($this->request->getBody()->getContents(), true);
            $tokenDataFromDb = $tokRep->getTokenFromDB($params['token']);
            if (!isset($tokenDataFromDb[0]->token) || $tokenDataFromDb[0]->dateTimeToDelete < (new \DateTime())->format('Y-m-d H:i:s')){
                return $this->response->withStatus(404);
            }
        }catch (\Exception $err){
            return $this->response->withStatus(404);
        }

        return $this->response->withStatus(200);
    }
}