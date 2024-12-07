<?php

// Phpunit test environment

return function (array $settings): array {
    $settings['error']['display_error_details'] = true;

    // Database
    $settings['db']['database.sqlite'] = 'slim_skeleton_test';

    return $settings;
};
