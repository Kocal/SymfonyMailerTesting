<?php

declare(strict_types=1);

$finder = PhpCsFixer\Finder::create()
    ->in([__DIR__.'/src', __DIR__.'/tests', __DIR__.'/fixtures/applications/Symfony/src'])
    ->name('*.php');

return (new PhpCsFixer\Config())
    ->setFinder($finder)
    ->setRiskyAllowed(true)
    ->setRules([
        '@Symfony'                              => true,
        'binary_operator_spaces'                => [
            'operators' => ['=>' => 'align', '=' => 'align'],
        ],
        'no_unreachable_default_argument_value' => false,
        'heredoc_to_nowdoc'                     => false,
        'declare_strict_types'                  => true,
    ]);
