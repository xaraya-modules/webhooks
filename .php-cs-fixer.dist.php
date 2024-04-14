<?php

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__)
    ->exclude([
        'coverage',
        'var',
        'vendor',
    ])
;

return (new PhpCsFixer\Config())
    ->setRules([
        '@PER' => true,
        '@PHP81Migration' => true,
    ])
    ->setFinder($finder)
;
