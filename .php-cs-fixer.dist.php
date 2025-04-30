<?php

$finder = PhpCsFixer\Finder::create()
    ->in([
        __DIR__.'/src',
        __DIR__.'/tests',
    ])
    ->exclude([
        'vendor',
    ]);

return (new PhpCsFixer\Config())
    ->setRiskyAllowed(true)
    ->setRules([
        '@Symfony' => true,
        '@Symfony:risky' => true,
        'declare_strict_types' => false,
        'strict_param' => true,
        'phpdoc_to_comment' => false,
        'phpdoc_align' => ['align' => 'left'],
        'ordered_imports' => ['sort_algorithm' => 'alpha'],
        'single_import_per_statement' => true,
        'no_unused_imports' => true,
        'no_superfluous_phpdoc_tags' => true,
        'phpdoc_separation' => false,
        'phpdoc_summary' => false,
        'no_blank_lines_after_phpdoc' => true,
        'yoda_style' => true,
    ])
    ->setFinder($finder);
