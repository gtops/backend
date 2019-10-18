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

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
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
            $trialAction = new GetListTrialByGenderAndAgeAction($c->get(DataBase::class));
            return $trialAction;
        },
        GetSecondResultOfTrialByFirstResultAction::class => function(ContainerInterface $c){
            $capsule = $c->get(DataBase::class);
            $trialAction = new GetSecondResultOfTrialByFirstResultAction();
            return $trialAction;
        },
        SwaggerWatcher::class => function(ContainerInterface $c){
            $swaggerAction = new Swagger\SwaggerAction($c->get('settings')['pathToProject']);
            return $swaggerAction;
        }
    ]);
};
