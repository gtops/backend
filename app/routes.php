<?php
declare(strict_types=1);

use Slim\App;
use App\Swagger\SwaggerWatcher;
use App\Application\Actions\Trial\GetListTrialByGenderAndAgeAction;
use App\Application\Actions\Trial\GetSecondResultOfTrialByFirstResultAction;
use App\Application\Actions\Role\GetRoleAction;
use App\Application\Actions\User\SendInviteAction;
use App\Application\Actions\User\InviteValidationAction;
use App\Application\Actions\User\RegistrationAction;
use App\Application\Actions\User\LoginAction;
use \App\Application\Actions\User\GetNewTokensAction;
use \App\Application\Actions\User\AuthAction;
use App\Application\Actions\Trial\TrialAction;
use App\Application\Actions\Role\RoleAction;
use App\Application\Actions\Invite\InviteAction;
use App\Application\Actions\Organization\OrganizationAction;
use App\Application\Actions\LocalAdmin\LocalAdminAction;
use App\Application\Actions\Event\EventAction;
use App\Application\Actions\Team\TeamAction;
use App\Application\Actions\Secretary\SecretaryAction;

return function (App $app) {
    $app->options('/{routes:.+}', function ($request, $response, $args) {
        return $response;
    });

    $app->post('/api/v1/invite', InviteAction::class.':sendInviteToOrganization');
    $app->post('/api/v1/invite/isValid', InviteAction::class.':validate');

    //работа с пользователями
    $app->post('/api/v1/auth/registration', AuthAction::class.':registration');
    $app->post('/api/v1/auth/login', AuthAction::class.':login');
    $app->post('/api/v1/auth/refresh', AuthAction::class.':refresh');
    $app->post('/api/v1/auth/invite', InviteAction::class.':sendInviteToRegistration');
    $app->post('/api/v1/auth/confirmAccount', AuthAction::class.':confirmAccount');

    //result
    $app->get('/api/v1/trial/{age:[0-9]+}/{gender:[0-9]+}', TrialAction::class.':getTrialsByGenderAndAge');
    $app->get('/api/v1/trial/{id:[0-9]+}/firstResult', TrialAction::class.':getSecondResult');
    $app->get('/docs', SwaggerWatcher::class.':getNewDocs');

    //organization
    $app->post('/api/v1/organization', OrganizationAction::class.':add');
    $app->get('/api/v1/organization/{id:[0-9]+}', OrganizationAction::class.':get');
    $app->delete('/api/v1/organization/{id:[0-9]+}', OrganizationAction::class.':delete');
    $app->get('/api/v1/organization', OrganizationAction::class.':getAll');
    $app->put('/api/v1/organization/{id:[0-9]+}', OrganizationAction::class.':update');

    //localAdmin
    $app->post('/api/v1/organization/{id:[0-9]+}/localAdmin/existingAccount', LocalAdminAction::class.':addExistingAccount');
    $app->post('/api/v1/organization/{id:[0-9]+}/localAdmin', LocalAdminAction::class.':add');
    $app->get('/api/v1/organization/{id:[0-9]+}/localAdmin', LocalAdminAction::class.':getAll');
    $app->get('/api/v1/organization/{id:[0-9]+}/localAdmin/{idLocalAdmin:[0-9]+}', LocalAdminAction::class.':get');
    $app->delete('/api/v1/organization/{id:[0-9]+}/localAdmin/{idLocalAdmin:[0-9]+}', LocalAdminAction::class.':delete');
    $app->put('/api/v1/organization/{id:[0-9]+}/localAdmin/{idLocalAdmin:[0-9]+}', OrganizationAction::class.':update');

    //Event
    $app->post('/api/v1/organization/{id:[0-9]+}/event', EventAction::class.':add');
    $app->delete('/api/v1/organization/{id:[0-9]+}/event/{eventId:[0-9]+}', EventAction::class.':delete');
    $app->get('/api/v1/organization/{id:[0-9]+}/event/{eventId:[0-9]+}', EventAction::class.':get');
    $app->put('/api/v1/organization/{id:[0-9]+}/event/{eventId:[0-9]+}', EventAction::class.':update');
    $app->get('/api/v1/organization/{id:[0-9]+}/event', EventAction::class.':getAll');

    //Team
    $app->post('/api/v1/organization/{id:[0-9]+}/event/{eventId:[0-9]+}/team', TeamAction::class.':add');
    $app->get('/api/v1/organization/{id:[0-9]+}/event/{eventId:[0-9]+}/team', TeamAction::class.':getAll');
    $app->get('/api/v1/organization/{id:[0-9]+}/event/{eventId:[0-9]+}/team/{teamId:[0-9]+}', TeamAction::class.':get');
    $app->delete('/api/v1/organization/{id:[0-9]+}/event/{eventId:[0-9]+}/team/{teamId:[0-9]+}', TeamAction::class.':delete');
    $app->put('/api/v1/organization/{id:[0-9]+}/event/{eventId:[0-9]+}/team/{teamId:[0-9]+}', TeamAction::class.':update');

    //Secretary
    $app->post('/api/v1/organization/{id:[0-9]+}/event/{eventId:[0-9]+}/secretary', SecretaryAction::class.':add');
    $app->post('/api/v1/organization/{id:[0-9]+}/event/{eventId:[0-9]+}/secretary/existingAccount', SecretaryAction::class.':addExistingAccount');
    $app->get('/api/v1/organization/{id:[0-9]+}/event/{eventId:[0-9]+}/secretary', SecretaryAction::class.':get');
    $app->delete('/api/v1/organization/{id:[0-9]+}/event/{eventId:[0-9]+}/secretary/{secretaryId:[0-9]+}', SecretaryAction::class.':delete');

    //роли
    $app->get('/api/v1/role', RoleAction::class.':getList');

};
