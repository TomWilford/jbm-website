<?php

include __DIR__ . '/vendor/autoload.php';

$cssMinifier = new MatthiasMullie\Minify\CSS();

$cssPath = __DIR__ . '/resources/css/*.css';
$cssIterator = new GlobIterator($cssPath);

foreach ($cssIterator as $file) {
    $cssMinifier->add($file->getRealPath());
}

$cssMinifier->minify(__DIR__ . '/public/css/style.css');

$jsMinifier = new MatthiasMullie\Minify\JS();

$jsPath = __DIR__ . '/resources/js/*.js';
$jsIterator = new GlobIterator($jsPath);
foreach ($jsIterator as $file) {
    $jsMinifier->add($file->getRealPath());
}

$jsMinifier->minify(__DIR__ . '/public/js/app.js');
