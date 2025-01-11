<?php

use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Tools\DsnParser;

$settings = require __DIR__ . '/config/settings.php';

$dsnParser = new DsnParser();
$connectionParams = $dsnParser->parse($settings['db']['dsn']);

return DriverManager::getConnection($connectionParams);
