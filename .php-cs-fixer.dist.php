<?php

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__ . '/src');

$config = new PhpCsFixer\Config();

return $config->setRules([
    '@PSR1' => true,
    'array_syntax' => ['syntax' => 'short'],
    'ordered_imports' => true,
    'blank_line_after_namespace' => true,
    'blank_line_after_opening_tag' => true,
    'braces' => ['allow_single_line_closure' => false],
    'function_declaration' => ['closure_function_spacing' => 'none'],
    'multiline_comment_opening_closing' => true,
    'phpdoc_trim_consecutive_blank_line_separation' => true,
    'phpdoc_separation' => true,
    'phpdoc_add_missing_param_annotation' => true,
    'blank_line_before_statement' => ['statements' => ['return']],
])
    ->setFinder($finder);