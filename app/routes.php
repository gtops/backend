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

return function (App $app) {
    $app->options('/{routes:.+}', function ($request, $response, $args) {
        return $response;
    });

    $app->post('/organization/invite', SendInviteAction::class);
    $app->post('/invite/isValid', InviteValidationAction::class);

    $app->group('/api/v1/auth', function (){
        $this->post('/registration', AuthAction::class.':registration');
        $this->post('/login', AuthAction::class.':login');
        $this->post('/refresh', AuthAction::class.':refresh');
    });

    $app->get('/api/v1/trial/{age:[0-9]+}/{gender:[0-9]+}', TrialAction::class.':getTrialsByGenderAndAge');
    $app->get('/api/v1/trial/{id:[0-9]+}/firstResult/{firstResult:[0-9]+}', TrialAction::class.':getSecondResult');
    $app->get('/docs', SwaggerWatcher::class.':getNewDocs');

    $app->get('api/v1/role', RoleAction::class.':getList');
//инвайт
//валидация инвайта

};
