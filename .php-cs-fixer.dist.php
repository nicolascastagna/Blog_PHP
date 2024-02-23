<?php

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__ . '/src');

$config = new PhpCsFixer\Config();

return $config->setRules([
    'array_syntax' => ['syntax' => 'short'],
    'ordered_imports' => true,
    'blank_line_after_namespace' => true,
    'blank_line_after_opening_tag' => true,
    'braces' => ['allow_single_line_closure' => false],
    'function_declaration' => ['closure_function_spacing' => 'none'],
    'phpdoc_trim_consecutive_blank_line_separation' => true,
    'phpdoc_separation' => true,
    'phpdoc_add_missing_param_annotation' => true,
    'no_empty_phpdoc' => true,
    'strict_param' => true,
    'combine_consecutive_issets' => true,
    'combine_consecutive_unsets' => true,
    'clean_namespace' => true,
    'phpdoc_to_param_type' => true,
    'return_type_declaration' => true,
    'use_arrow_functions' => true,
    'fully_qualified_strict_types' => true,
    'global_namespace_import' => true,
    'no_leading_import_slash' => true,
    'no_unneeded_import_alias' => true,
    'no_unused_imports' => true,
    'no_whitespace_before_comma_in_array' => true,
    'trim_array_spaces' => true,
    'whitespace_after_comma_in_array' => true,
    'attribute_empty_parentheses' => true,
    'braces_position' => true,
    'array_push' => true,
    'no_unneeded_curly_braces' => true,
    'no_useless_concat_operator' => true,
    'no_useless_nullsafe_operator' => true,
    'operator_linebreak' => [
        'only_booleans' => true,
    ],
    'no_useless_return' => true,
    'return_assignment' => true,
    'no_empty_statement' => true,
    'array_indentation' => true,
    'indentation_type' => true,
    'method_chaining_indentation' => true,
    'no_whitespace_in_blank_line' => true,
    'spaces_inside_parentheses' => [
        'space' => 'none',
    ],
    'no_spaces_around_offset' => [
        'positions' => ['inside', 'outside'],
    ],
    'no_extra_blank_lines' => [
        'tokens' => [
            'attribute',
            'case',
            'continue',
            'curly_brace_block',
            'default',
            'extra',
            'parenthesis_brace_block',
            'square_brace_block',
            'switch',
            'throw',
            'use',
        ],
    ],
    'blank_line_before_statement' => [
        'statements' => ['break', 'continue', 'declare', 'return', 'throw', 'try'],
    ],
    'align_multiline_comment' => true,
    'no_blank_lines_after_phpdoc' => true,
    'phpdoc_indent' => true,
    'phpdoc_line_span' => true,
    'phpdoc_no_empty_return' => true,
    'phpdoc_param_order' => true,
    'phpdoc_tag_casing' => true,
    'phpdoc_var_annotation_correct_order' => true,
    'phpdoc_var_without_name' => true,
    'phpdoc_no_useless_inheritdoc' => false,
    'phpdoc_types_order' => [
        'null_adjustment' => 'always_last',
        'sort_algorithm' => 'alpha',
        'case_sensitive' => true,
    ],
    'phpdoc_order' => [
        'order' => ['param', 'return', 'throws'],
    ],
])
    ->setRiskyAllowed(true)
    ->setFinder($finder);
