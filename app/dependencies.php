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
use App\Application\Actions\Trial\GetListTrialByGenderAndAgeAction;
use Illuminate\Database\Capsule\Manager as Capsule;
use App\Application\Actions\Trial\GetSecondResultOfTrialByFirstResultAction;
use App\Services\Validators\GetTrialsRouteValidator;
use \App\Services\Validators\GetSecondResultRouteValidator;
use App\Application\Actions\Role\GetRoleAction;
use App\Services\EmailSendler\EmailSendler;
use App\Application\Actions\User\SendInviteAction;
use App\Services\Token\Token;
use App\Application\Actions\User\InviteValidationAction;
use App\Application\Actions\User\RegistrationAction;
use App\Services\Validators\RegistrationRouteValidator;
use App\Application\Actions\User\LoginAction;
use App\Services\Validators\LoginValidator;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        LoginAction::class => function(ContainerInterface $c){
            $c->get(DataBase::class);
            $validator = new LoginValidator();
            $loginAction = new LoginAction($c->get(Token::class), $validator);
            return $loginAction;
        },
        RegistrationAction::class => function(ContainerInterface $c){
            $c->get(DataBase::class);
            $validator = new RegistrationRouteValidator();
            $regAction = new RegistrationAction($c->get(Token::class), $validator);
            return $regAction;
        },
        InviteValidationAction::class => function(ContainerInterface $c){
            $c->get(DataBase::class);
            $inviteValidateAction = new InviteValidationAction($c->get(Token::class));
            return $inviteValidateAction;
        },
        SendInviteAction::class => function(ContainerInterface $c){
            $c->get(DataBase::class);
            $validator = new \App\Services\Validators\SendInviteValidator();
            $sendInviteAction = new SendInviteAction($c->get(EmailSendler::class), $c->get(Token::class), $validator);
            return $sendInviteAction;
        },
        Token::class => function(ContainerInterface $c){
            return new Token($c->get('privateSettings')['Token']);
        },
        EmailSendler::class => function(ContainerInterface $c){
            return new EmailSendler($c->get('privateSettings')['Mailer']);
        },
        GetRoleAction::class => function(ContainerInterface $c){
            $db = new DataBase($c->get('privateSettings')['DB']);
            $roleAction = new \App\Application\Actions\Role\GetRoleAction();
            return $roleAction;
        },
        GetTrialsRouteValidator::class => function(){
            return new GetTrialsRouteValidator();
        },
        GetSecondResultRouteValidator::class => function(){
            return new \App\Services\Validators\GetSecondResultRouteValidator();
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
        GetListTrialByGenderAndAgeAction::class => function(ContainerInterface $c)
        {
            $trialAction = new GetListTrialByGenderAndAgeAction($c->get(DataBase::class), $c->get(GetTrialsRouteValidator::class));
            return $trialAction;
        },
        GetSecondResultOfTrialByFirstResultAction::class => function(ContainerInterface $c){
            $capsule = $c->get(DataBase::class);

            $trialAction = new GetSecondResultOfTrialByFirstResultAction($c->get(GetSecondResultRouteValidator::class));
            return $trialAction;
        },
        SwaggerWatcher::class => function(ContainerInterface $c){
            $swaggerAction = new Swagger\SwaggerAction($c->get('settings')['pathToProject']);
            return $swaggerAction;
        }
    ]);
};
