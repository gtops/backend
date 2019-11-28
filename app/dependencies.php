<?php
declare(strict_types=1);

use DI\ContainerBuilder;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use App\Swagger\SwaggerWatcher;
use App\Application\Actions\Swagger;
use App\Persistance\ModelsEloquant\DataBase;
use Illuminate\Database\Capsule\Manager as Capsule;
use App\Services\EmailSendler\EmailSendler;
use App\Application\Actions\User\SendInviteAction;
use App\Services\Token\Token;
use App\Application\Actions\User\InviteValidationAction;
use \App\Application\Actions\User\AuthAction;
use \App\Persistance\Repositories\User\UserRepository;
use App\Persistance\Repositories\User\RefreshTokenRepository;
use App\Persistance\Repositories\User\RegistrationTokenRepository;
use App\Application\Actions\Trial\TrialAction;
use App\Services\Trial\Trial;
use App\Persistance\Repositories\TrialRepository\TrialRepository;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        AuthAction::class => function(ContainerInterface $c){
            $c->get(Token::class);
            $authService = new \App\Services\Auth\Auth($c->get(UserRepository::class), new RefreshTokenRepository(), new RegistrationTokenRepository());
            $auth = new AuthAction($authService);
            return $auth;
        },
        TrialAction::class => function(ContainerInterface $c){
            $c->get(DataBase::class);
            return new TrialAction(new Trial(new TrialRepository()));
        },
        UserRepository::class => function(ContainerInterface $c){
            $c->get(DataBase::class);
            return new UserRepository();
        },
        InviteValidationAction::class => function(ContainerInterface $c){
            $c->get(Token::class);
            $c->get(DataBase::class);
            $inviteValidateAction = new InviteValidationAction();
            return $inviteValidateAction;
        },
        SendInviteAction::class => function(ContainerInterface $c){
            $c->get(DataBase::class);
            $validator = new \App\Services\Validators\SendInviteValidator();
            $c->get(Token::class);
            $sendInviteAction = new SendInviteAction($c->get(EmailSendler::class), $validator);
            return $sendInviteAction;
        },
        Token::class => function(ContainerInterface $c){
            Token::$key = $c->get('privateSettings')['Token']['key'];
        },
        EmailSendler::class => function(ContainerInterface $c){
            return new EmailSendler($c->get('privateSettings')['Mailer']);
        },
        LoggerInterface::class => function (ContainerInterface $c) {
            $settings = $c->get('settings');

            $loggerSettings = $settings['logger'];
            $logger = new Logger($loggerSettings['name']);

            $processor = new UidProcessor();
            $logger->pushProcessor($processor);

            $handler = new StreamHandler($loggerSettings['path'], $loggerSettings['level']);
            $logger->pushHandler($handler);

            return $logger;
        },
        DataBase::class => function(ContainerInterface $c):Capsule{
            $db = new DataBase($c->get('privateSettings')['DB']);
            return $db->getCapsule();
        },
        SwaggerWatcher::class => function(ContainerInterface $c){
            $logger = new Logger('a');
            $swaggerAction = new Swagger\SwaggerAction($c->get('settings')['pathToProject']);
            $logger->alert('refre');
            return $swaggerAction;
        }
    ]);
};
