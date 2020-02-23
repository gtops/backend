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
use App\Application\Actions\Role\RoleAction;
use App\Persistance\Repositories\Role\RoleRepository;
use App\Application\Actions\Invite\InviteAction;
use App\Services\Invite\Invite;
use App\Services\Role\Role;
use App\Services\Auth\Auth;
use App\Validators;
use App\Validators\Invite\InviteValidator;
use App\Validators\Auth\RegistrationValidator;
use App\Services\Organization\OrganiztionService;
use App\Application\Actions\Organization\OrganizationAction;
use App\Persistance\Repositories\Organization\OrganizationRepository;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        OrganizationRepository::class => function(ContainerInterface $c)
        {
            $c->get(DataBase::class);
            return new OrganizationRepository();
        },
        OrganiztionService::class => function(ContainerInterface $c)
        {
            return new OrganiztionService($c->get(OrganizationRepository::class));
        },
        OrganizationAction::class => function(ContainerInterface $c)
        {
            return new OrganizationAction($c->get(OrganiztionService::class));
        },
        InviteAction::class => function(ContainerInterface $c){
            return new InviteAction($c->get(Invite::class));
        },
        Invite::class => function(ContainerInterface $c){
            $c->get(Token::class);
            return new Invite($c->get(RegistrationTokenRepository::class), $c->get(EmailSendler::class));
        },
        RoleAction::class => function(ContainerInterface $c){
            return new RoleAction($c->get(Role::class));
        },
        AuthAction::class => function(ContainerInterface $c){
            $c->get(Token::class);
            return new AuthAction($c->get(Auth::class));
        },
        Auth::class => function(ContainerInterface $c){
            return new Auth($c->get(UserRepository::class), $c->get(RefreshTokenRepository::class), $c->get(RegistrationTokenRepository::class));
        },
        RegistrationTokenRepository::class => function(ContainerInterface $c){
            $c->get(DataBase::class);
            return new RegistrationTokenRepository();
        },
        RefreshTokenRepository::class => function(ContainerInterface $c){
            $c->get(DataBase::class);
            return new RefreshTokenRepository();
        },
        Role::class => function(ContainerInterface $c){
            return new Role($c->get(RoleRepository::class));
        },
        RoleRepository::class => function(ContainerInterface $c){
            $c->get(DataBase::class);
            return new RoleRepository();
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
