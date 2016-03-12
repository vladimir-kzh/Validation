<?php

use Symfony\CS\Config\Config;
use Symfony\CS\Finder\DefaultFinder;
use Symfony\CS\FixerInterface;

$finder = DefaultFinder::create()
    ->files()
    ->name('*.php')
    ->name('*.phpt')
    ->in('library')
    ->in('tests');

return Config::create()
    ->level(FixerInterface::SYMFONY_LEVEL)
    ->fixers(
        [
            'newline_after_open_tag',
            'ordered_use',
            'phpdoc_order',
            'short_array_syntax',
        ]
    )
    ->finder($finder);
