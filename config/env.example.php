<?php

declare(strict_types=1);

return function (array $settings): array {
    // Database credentials
    $settings['db']['username'] = 'root';
    $settings['db']['password'] = '';

    // Docker example
    // if (isset($_ENV['DOCKER'])) {
    //    $settings['db']['host'] = $_ENV['MYSQL_HOST'] ?? 'host.docker.internal';
    //    $settings['db']['port'] = $_ENV['MYSQL_PORT'] ?? '3306';
    //    $settings['db']['username'] = $_ENV['MYSQL_USER'] ?? 'root';
    //    $settings['db']['password'] = $_ENV['MYSQL_PASSWORD'] ?? '';
    // }

    return $settings;
};
