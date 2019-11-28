<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 28.11.2019
 * Time: 10:24
 */

namespace App\Services\Invite;
use App\Application\Actions\ActionError;
use App\Persistance\Repositories\User\RegistrationTokenRepository;
use App\Services\EmailSendler\EmailSendler;
use App\Services\Token\Token;
use Psr\Http\Message\ResponseInterface as Response;

class Invite
{
    private $regTokenRep;
    private $emailSendler;

    public function __construct(RegistrationTokenRepository $registrationTokenRepository, EmailSendler $emailSendler)
    {
        $this->emailSendler = $emailSendler;
        $this->regTokenRep = $registrationTokenRepository;
    }

    public function sendInviteToOrganization(array $params, Response $response):Response
    {
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

        $this->regTokenRep->addTokenToDB($token);

        try {
            $this->emailSendler->sendInvite($params['email'], $token);
        }catch (\Exception $err){
            $response->getBody()->write(json_encode(new ActionError(ActionError::BAD_REQUEST, 'invalid email')));
            return $response->withStatus(400);
        }

        return $response;
    }
}