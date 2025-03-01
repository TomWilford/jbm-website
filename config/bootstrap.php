<?php

use DI\ContainerBuilder;
use Slim\App;
use Sqids\Sqids;
use TomWilford\SlimSqids\GlobalSqidConfiguration;

require_once __DIR__ . '/../vendor/autoload.php';

// Build DI container instance
$container = (new ContainerBuilder())
    ->addDefinitions(__DIR__ . '/container.php')
    ->build();

GlobalSqidConfiguration::set($container->get(Sqids::class));

// Create App instance
return $container->get(App::class);
