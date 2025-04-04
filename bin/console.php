<?php

use App\Application\Console\QueryCommand;
use App\Application\Console\SeedCommand;
use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArgvInput;

require_once __DIR__ . '/../vendor/autoload.php';

$env = (new ArgvInput())->getParameterOption(['--env', '-e'], 'dev');

if ($env) {
    $_ENV['APP_ENV'] = $env;
}

/** @var ContainerInterface $container */
$container = (new ContainerBuilder())
    ->addDefinitions(__DIR__ . '/../config/container.php')
    ->build();

try {
    /** @var Application $application */
    $application = $container->get(Application::class);

    // Register your console commands here
    $application->add($container->get(SeedCommand::class));
    $application->add($container->get(QueryCommand::class));

    exit($application->run());
} catch (Throwable $exception) {
    echo $exception->getMessage();
    exit(1);
}

