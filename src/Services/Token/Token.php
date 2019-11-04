<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 04.11.2019
 * Time: 1:53
 */

namespace App\Services\Token;
use \Firebase\JWT\JWT;

class Token
{
    private $key;

    public function __construct(array $option)
    {
        $this->key = $option['key'];
    }

    public function getEncodedToken(array $tokenData, $leeway):string
    {
        JWT::$leeway = $leeway;
        return JWT::encode($tokenData, $this->key);
    }

    public function getDecodedToken(string $token, $leeway)
    {
        JWT::$leeway = $leeway;
        return JWT::decode($token, $this->key, array('HS256'));
    }

}