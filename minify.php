<?php

include __DIR__ . '/vendor/autoload.php';

$minifier = new MatthiasMullie\Minify\CSS();

$cssIterator = new DirectoryIterator(__DIR__ . '/resources/css/');
foreach ($cssIterator as $file) {
    if (!$file->isDot()) {
        $minifier->add($file->getRealPath());
    }
}

$minifier->minify(__DIR__ . '/public/css/style.css');