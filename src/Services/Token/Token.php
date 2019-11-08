<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 04.11.2019
 * Time: 1:53
 */

namespace App\Services\Token;
use \Firebase\JWT\JWT;
use Monolog\Logger;

class Token
{
    private $key;

    public function __construct(array $option)
    {
        $this->key = $option['key'];
    }

    public function getEncodedToken(array $tokenData):string
    {
        return JWT::encode($tokenData, $this->key);
    }

    public function getDecodedToken(string $token)
    {
        return JWT::decode($token, $this->key, array('HS256'));
    }

    public function getEncodedPassword(string $password)
    {
        return crypt($password, $this->key);
    }

    public function isOldToken($tokenInArray):bool{
        $leeway = $tokenInArray['liveTime'];

        $tokenDate = new \DateTime($tokenInArray['addedTime']);
        $tokenDate->add(new \DateInterval('PT'.$leeway.'120S'));

        $newDate = (new \DateTime())->setTimezone(new \DateTimeZone('europe/moscow'));

        if ($newDate->format('Y-m-d H:i:s') > $tokenDate->format('Y-m-d H:i:s')){
            return true;
        }

        return false;
    }
}