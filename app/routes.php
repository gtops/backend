<?php
declare(strict_types=1);

use Slim\App;
use App\Swagger\SwaggerWatcher;
use App\Application\Actions\Trial\TrialAction;

return function (App $app) {
    $app->get('/trial', TrialAction::class);

    $app->get('/docs', SwaggerWatcher::class);
};
