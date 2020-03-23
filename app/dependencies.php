<?php
declare(strict_types=1);

use App\Application\Actions\EventParticipant\EventParticipantAction;
use App\Persistance\Repositories\EventParticipant\EventParticipantRepository;
use App\Services\AccessService\AccessService;
use App\Services\EventParticipant\EventParticipantService;
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
use App\Services\Token\Token;
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
use App\Services\Organization\OrganizationService;
use App\Application\Actions\Organization\OrganizationAction;
use App\Persistance\Repositories\Organization\OrganizationRepository;
use App\Persistance\Repositories\LocalAdmin\LocalAdminRepository;
use App\Services\LocalAdmin\LocalAdminService;
use App\Application\Actions\LocalAdmin\LocalAdminAction;
use App\Application\Actions\Event\EventAction;
use App\Services\Event;
use App\Services\Event\EventService;
use App\Persistance\Repositories\Event\EventRepository;
use App\Persistance\Repositories\Team\TeamRepository;
use App\Services\Team\TeamService;
use App\Application\Actions\Team\TeamAction;
use App\Services\Secretary\SecretaryService;
use App\Persistance\Repositories\Secretary\SecretaryRepository;
use App\Application\Actions\Secretary\SecretaryAction;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        SecretaryAction::class => function(ContainerInterface $c){
            return new SecretaryAction($c->get(SecretaryService::class));
        },
        EventParticipantAction::class => function(ContainerInterface $c){
            return new EventParticipantAction($c->get(AccessService::class), $c->get(EventParticipantService::class));
        },
        EventParticipantService::class => function(ContainerInterface $c){
            return new EventParticipantService($c->get(EventParticipantRepository::class), $c->get(UserRepository::class));
        },
        SecretaryService::class => function(ContainerInterface $c){
            return new SecretaryService(
                $c->get(SecretaryRepository::class),
                $c->get(UserRepository::class),
                $c->get(OrganizationRepository::class),
                $c->get(LocalAdminRepository::class),
                $c->get(EventRepository::class),
                $c->get(RoleRepository::class)
            );
        },
        SecretaryRepository::class => function(ContainerInterface $c){
            $c->get(DataBase::class);
            return new SecretaryRepository();
        },
        LocalAdminAction::class => function(ContainerInterface $c)
        {
            return new LocalAdminAction($c->get(LocalAdminService::class));
        },
        LocalAdminService::class => function(ContainerInterface $c)
        {
            return new LocalAdminService($c->get(LocalAdminRepository::class), $c->get(RoleRepository::class), $c->get(UserRepository::class), $c->get(OrganizationRepository::class));
        },
        LocalAdminRepository::class => function(ContainerInterface $c)
        {
            $c->get(DataBase::class);
            return new LocalAdminRepository();
        },
        OrganizationRepository::class => function(ContainerInterface $c)
        {
            $c->get(DataBase::class);
            return new OrganizationRepository();
        },
        OrganizationService::class => function(ContainerInterface $c)
        {
            return new OrganizationService(
                $c->get(OrganizationRepository::class),
                $c->get(SecretaryRepository::class),
                $c->get(LocalAdminRepository::class),
                $c->get(UserRepository::class),
                $c->get(RoleRepository::class)
            );
        },
        OrganizationAction::class => function(ContainerInterface $c)
        {
            return new OrganizationAction($c->get(OrganizationService::class));
        },
        TeamAction::class => function(ContainerInterface $c){
            return new TeamAction($c->get(TeamService::class), $c->get(AccessService::class));
        },
        AccessService::class => function(ContainerInterface $c){
            return new AccessService(
                $c->get(UserRepository::class),
                $c->get(LocalAdminRepository::class),
                $c->get(SecretaryRepository::class),
                $c->get(OrganizationRepository::class),
                $c->get(RoleRepository::class),
                $c->get(EventRepository::class),
                $c->get(EventParticipantRepository::class)
            );
        },
        TeamService::class => function(ContainerInterface $c){
            return new TeamService($c->get(UserRepository::class), $c->get(TeamRepository::class));
        },
        EventService::class => function(ContainerInterface $c)
        {
            return new EventService(
                $c->get(LocalAdminRepository::class),
                $c->get(EventRepository::class),
                $c->get(SecretaryRepository::class),
                $c->get(RoleRepository::class),
                $c->get(UserRepository::class),
                $c->get(EventParticipantRepository::class)
            );
        },
        EventAction::class => function(ContainerInterface $c)
        {
            return new EventAction($c->get(EventService::class), $c->get(AccessService::class));
        },
        InviteAction::class => function(ContainerInterface $c){
            return new InviteAction($c->get(Invite::class));
        },
        Invite::class => function(ContainerInterface $c){
            $c->get(Token::class);
            return new Invite($c->get(RegistrationTokenRepository::class), $c->get(EmailSendler::class), $c->get(UserRepository::class), $c->get(RoleRepository::class));
        },
        RoleAction::class => function(ContainerInterface $c){
            return new RoleAction($c->get(Role::class));
        },
        AuthAction::class => function(ContainerInterface $c){
            $c->get(Token::class);
            return new AuthAction($c->get(Auth::class));
        },
        Auth::class => function(ContainerInterface $c){
            return new Auth($c->get(UserRepository::class), $c->get(RefreshTokenRepository::class), $c->get(RegistrationTokenRepository::class), $c->get(LocalAdminRepository::class));
        },
        TeamRepository::class => function(ContainerInterface $c){
            $c->get(DataBase::class);
            return new TeamRepository();
        },
        RegistrationTokenRepository::class => function(ContainerInterface $c){
            $c->get(DataBase::class);
            return new RegistrationTokenRepository();
        },
        EventParticipantRepository::class => function(ContainerInterface $c){
            $c->get(DataBase::class);
            return new EventParticipantRepository();
        },
        RefreshTokenRepository::class => function(ContainerInterface $c){
            $c->get(DataBase::class);
            return new RefreshTokenRepository();
        },
        Role::class => function(ContainerInterface $c){
            return new Role($c->get(RoleRepository::class));
        },
        EventRepository::class => function(ContainerInterface $c) {
            $c->get(DataBase::class);
            return new EventRepository();
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
