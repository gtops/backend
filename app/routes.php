<?php
declare(strict_types=1);

use Slim\App;
use App\Swagger\SwaggerWatcher;
use App\Application\Actions\Trial\GetListTrialByGenderAndAgeAction;
use App\Application\Actions\Trial\GetSecondResultOfTrialByFirstResultAction;

return function (App $app) {
    $app->get('/trial', GetListTrialByGenderAndAgeAction::class);

    $app->get('/docs', SwaggerWatcher::class);
    $app->get('/trial/result', GetSecondResultOfTrialByFirstResultAction::class);
};
