<?php
declare(strict_types=1);

namespace App\Application\Middleware;

use App\Services\Logger;
use App\Services\Token\Token;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class AuthorizeMiddleware implements Middleware
{
    /**
     * {@inheritdoc}
     */
    public function process(Request $request, RequestHandler $handler): Response
    {
        $token = $request->getHeader('Authorization')[0] ?? '';

        $config = json_decode(file_get_contents(__DIR__.'/../../../config.json'), true);
        Token::$key = $config['Token']['key'];
        try{
            $tokenInArray = Token::getDecodedToken($token);
            if (Token::isOldToken($tokenInArray)){
                $request = $request->withHeader('error', $tokenInArray['old token']);
            }

            $request = $request->withHeader('userEmail', $tokenInArray['email']);
            $request = $request->withHeader('userRole', $tokenInArray['role']);

        }catch (\Exception $err){
            $request = $request->withHeader('error', 'invalid token');
        }


        return $handler->handle($request);
    }
}
