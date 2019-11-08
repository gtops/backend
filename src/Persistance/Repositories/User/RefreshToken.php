<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 05.11.2019
 * Time: 2:00
 */

namespace App\Persistance\Repositories\User;
use App\Persistance\ModelsEloquant\LoginToken\RefreshToken as RToken;

class RefreshToken
{
    public function refreshTokenIsSet(string $token):bool
    {
        $result = RToken::query()->where('token', '=', $token)->get();
        if (!isset($result[0]->token)){
            return false;
        }

        return true;
    }

    public function deleteRefreshToken(string $token):void
    {
        RToken::query()->where('token', '=', $token)->delete();
    }

    public function deleteRefreshTokenWithEmail(string $email):void
    {
        RToken::query()->where('email', '=', $email)->delete();
    }

    public function addRefreshToken(string $token, string $email):void
    {
        RToken::query()->create([
            'token' => $token,
            'email' => $email
        ]);
    }

    public function updateRefreshTokenWithEmail(string $email, string $token):void
    {
        RToken::query()->where('email', '=', $email)->update([
            'token' => $token
        ]);
    }
}