<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 04.11.2019
 * Time: 16:07
 */

namespace App\Persistance\Repositories\User;
use App\Persistance\ModelsEloquant\RegistrationToken\RegistrationToken as Token;

class RegistrationTokenRepository
{
    public function addTokenToDB($token):void
    {
        Token::query()->create([
            'token' => $token,
            'dateTimeToDelete' => (new \DateTime('+1 day'))
                ->setTimezone(new \DateTimeZone('europe/moscow'))
                ->format('Y-m-d H:i:s')
        ]);
    }

    public function cleanOldTokens()
    {

    }

    public function getTokenFromDB(string $token)
    {
       return Token::query()->where('token', '=', $token)->get();

    }

    public function deleteTokenFromDB(string $token):void
    {
        Token::query()->where('token', '=', $token)->delete();
    }
}