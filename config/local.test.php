<?php

// Phpunit test environment

return function (array $settings): array {
    $settings['error']['display_error_details'] = true;

    // Database
    $settings['db'] = [
        'connection' => 'sqlite',
        'dsn' => 'pdo-sqlite:///:memory:',
        'host' => 'localhost',
        'encoding' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
        // PDO options
        'options' => [
            PDO::ATTR_PERSISTENT => false,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_EMULATE_PREPARES => true,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ],
    ];

    $settings['api'] = [
        "path" => "/api",
        "realm" => "Protected",
        "secure" => false,
        "relaxed" => ["localhost"],
    ];
    $settings['api']['users']['test'] = 'test';

    return $settings;
};
