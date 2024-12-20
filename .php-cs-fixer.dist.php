<?php

$finder = (new PhpCsFixer\Finder())
    ->in([
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ])
    ->name('*.php')
;

return (new PhpCsFixer\Config())
    ->setRules([
        '@Symfony' => true,
        '@Symfony:risky' => true,
        '@PHP81Migration' => true,
        'strict_param' => true,
        'array_syntax' => ['syntax' => 'short'],
        'declare_strict_types' => true,
        'void_return' => true,
        'ordered_imports' => true,
        'no_unused_imports' => true,
        'fully_qualified_strict_types' => true,
    ])
    ->setRiskyAllowed(true)
    ->setFinder($finder)
;
