<?php

// Phpunit test environment

use App\Test\Fixtures\BitFixture;
use App\Test\Fixtures\ThingFixture;
use Sqids\Sqids;

return function (array $settings): array {
    $settings['error']['display_error_details'] = true;

    $settings['twig']['cache'] = false;

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
        'path' => '/api',
        'realm' => 'Protected',
        'secure' => false,
        'relaxed' => ['localhost'],
    ];
    $settings['api']['users']['test'] = 'test';

    $settings['fixtures'] = [
        ThingFixture::class,
        BitFixture::class,
    ];

    $settings['sqids'] = [
        'alphabet' => Sqids::DEFAULT_ALPHABET,
        'minLength' => Sqids::DEFAULT_MIN_LENGTH,
        'blockList' => Sqids::DEFAULT_BLOCKLIST,
    ];

    return $settings;
};
