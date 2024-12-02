<?php

include __DIR__ . '/vendor/autoload.php';

$cssMinifier = new MatthiasMullie\Minify\CSS();

$cssIterator = new DirectoryIterator(__DIR__ . '/resources/css/');
foreach ($cssIterator as $file) {
    if (!$file->isDot()) {
        $cssMinifier->add($file->getRealPath());
    }
}

$cssMinifier->minify(__DIR__ . '/public/css/style.css');

$jsMinifier = new MatthiasMullie\Minify\JS();

$jsIterator = new DirectoryIterator(__DIR__ . '/resources/js/');
foreach ($jsIterator as $file) {
    if (!$file->isDot()) {
        $jsMinifier->add($file->getRealPath());
    }
}

$jsMinifier->minify(__DIR__ . '/public/js/app.js');
