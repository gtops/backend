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
use \App\Application\Actions\User\Auth;

return function (App $app) {
    $app->options('/{routes:.+}', function ($request, $response, $args) {
        return $response;
    });

    $app->get('/trial', GetListTrialByGenderAndAgeAction::class);
    $app->post('/token/refresh', GetNewTokensAction::class);
    $app->get('/docs', SwaggerWatcher::class);
    $app->get('/trial/result', GetSecondResultOfTrialByFirstResultAction::class);

    $app->get('/role', GetRoleAction::class);
    $app->post('/organization/invite', SendInviteAction::class);
    $app->post('/invite/isValid', InviteValidationAction::class);
    $app->post('/registration', RegistrationAction::class);
    $app->post('/login', LoginAction::class);

    $app->post('/api/v1/auth/registration', Auth::class.':registration');
    $app->post('/api/v1/auth/login', Auth::class.':login');
    $app->post('/api/v1/auth/refresh', Auth::class.':refresh');
//    $app->get('api/v1/trial/{age}/{gender}', Trial::class.':getList');
//    $app->get('api/v1/trial/{id}/{firstResult}', Trial::class.':getSecondResult');
//    $app->get('api/v1/role', Role::class.':getList');
//инвайт
//валидация инвайта

};
