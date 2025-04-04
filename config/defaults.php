<?php

// Application default settings

// Error reporting
use Sqids\Sqids;

error_reporting(0);
ini_set('display_errors', '0');
ini_set('display_startup_errors', '0');

// Timezone
date_default_timezone_set('Europe/London');

// Set default locale
setlocale(LC_ALL, 'en_GB.UTF-8', 'en_GB');

$settings = [];

// Project root dir
$settings['root_dir'] = dirname(__DIR__, 1);

// Error handler
$settings['error'] = [
    // Should be set to false for the production environment
    'display_error_details' => false,
];

// Logger settings
$settings['logger'] = [
    // Log file location
    'path' => __DIR__ . '/../logs',
    // Default log level
    'level' => Psr\Log\LogLevel::DEBUG,
];

// Database settings
$settings['db'] = [
    'connection' => 'sqlite',
    'dsn' => 'pdo-sqlite:///' . dirname(__DIR__, 1) . '/database/database.sqlite',
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

$settings['doctrine']['migrations'] = [
    'table_storage' => [
        'table_name' => 'doctrine_migration_versions',
        'version_column_name' => 'version',
        'version_column_length' => 191,
        'executed_at_column_name' => 'executed_at',
        'execution_time_column_name' => 'execution_time',
    ],

    'migrations_paths' => [
        'App\Database\Migrations' => dirname(__DIR__, 1) . '/database/Migrations',
    ],

    'all_or_nothing' => true,
    'transactional' => true,
    'check_database_platform' => true,
    'organize_migrations' => 'none',
    'connection' => null,
    'em' => null,
];

// Twig
$settings['twig']['cache'] = dirname(__DIR__, 1) . '/var/cache';

// API
$settings['api'] = [
    'path' => '/api',
    'realm' => 'Protected',
    'secure' => true,
    'relaxed' => ['localhost'],
];
// $settings['api']['users']['username'] = 'password';

// Sqids
$settings['sqids'] = [
    'alphabet' => Sqids::DEFAULT_ALPHABET,
    'minLength' => Sqids::DEFAULT_MIN_LENGTH,
    'blockList' => Sqids::DEFAULT_BLOCKLIST,
];

return $settings;
