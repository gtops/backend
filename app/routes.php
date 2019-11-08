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

return function (App $app) {

    $app->get('/trial', GetListTrialByGenderAndAgeAction::class);

    $app->get('/docs', SwaggerWatcher::class);
    $app->get('/trial/result', GetSecondResultOfTrialByFirstResultAction::class);

    $app->get('/role', GetRoleAction::class);
    $app->post('/organization/invite', SendInviteAction::class);
    $app->post('/invite/isValid', InviteValidationAction::class);
    $app->post('/registration', RegistrationAction::class);
    $app->post('/login', LoginAction::class);

};
