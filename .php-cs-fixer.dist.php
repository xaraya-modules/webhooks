<?php

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__)
;

return (new PhpCsFixer\Config())
    ->setRules([
        '@PER' => true,
        '@PHP81Migration' => true,
    ])
    ->setFinder($finder)
;