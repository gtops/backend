<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 04.11.2019
 * Time: 18:17
 */

namespace App\Application\Actions\User;


use App\Application\Actions\Action;
use App\Domain\DomainException\DomainRecordNotFoundException;
use App\Persistance\Repositories\User\RegistrationTokenRepository;
use App\Services\Token\Token;
use Monolog\Logger;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpBadRequestException;

class InviteValidationAction extends Action
{
    public function __construct()
    {
    }

    /**
     * @return Response
     * @throws DomainRecordNotFoundException
     * @throws HttpBadRequestException
     */
    protected function action(): Response
    {
        $tokRep = new RegistrationTokenRepository();

        try{
            $params = json_decode($this->request->getBody()->getContents(), true);
            $tokenDataFromDb = $tokRep->getTokenFromDB($params['token']);
            if (!isset($tokenDataFromDb[0]->token) || $tokenDataFromDb[0]->dateTimeToDelete < (new \DateTime())->format('Y-m-d H:i:s')){
                return $this->response->withStatus(404);
            }
        }catch (\Exception $err){
            return $this->response->withStatus(404);
        }
        $tokenData = $decodedToken = (array)Token::getDecodedToken($params['token']);
        $this->response->getBody()->write(json_encode(['email' => $tokenData['email']]));
        return $this->response->withStatus(200);
    }
}