<?php

// Production environment

return function (array $settings): array {
    $settings['db']['database.sqlite'] = 'slim_skeleton';

    return $settings;
};
