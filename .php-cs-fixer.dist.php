<?php

$config = new PhpCsFixer\Config;
$config->getFinder()
    ->exclude('vendor')
    ->in(__DIR__);
$config->setRules([
    '@PSR2' => true,
    '@Symfony' => true,
    'binary_operator_spaces' => [],
    'braces_position' => [
        'functions_opening_brace' => 'same_line',
        'classes_opening_brace' => 'same_line'],
    'concat_space' => ['spacing' => 'one'],
    'fully_qualified_strict_types' => false,
    'no_superfluous_phpdoc_tags' => false,
    'no_unneeded_control_parentheses' => false,
    'phpdoc_align' => false,
    'single_line_comment_style' => false,
    'single_line_comment_spacing' => false,
    'single_quote' => false,
    'trailing_comma_in_multiline' => true,
    'visibility_required' => false,
    'yoda_style' => false
]);

return $config;
