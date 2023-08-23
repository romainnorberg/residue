<?php

/*
 * This file is part of the Residue package.
 * (c) Romain Norberg <romainnorberg@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$fileHeaderComment = <<<COMMENT
This file is part of the Residue package.
(c) Romain Norberg <romainnorberg@gmail.com>
For the full copyright and license information, please view the LICENSE
file that was distributed with this source code.
COMMENT;

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__)
    ->exclude('config')
;

return (new PhpCsFixer\Config())
    ->setRiskyAllowed(true)
    ->setRules([
        '@Symfony'       => true,
        '@Symfony:risky' => true,
        // Binary operators should be surrounded by space as configured.
        'binary_operator_spaces' => [
            'operators' => ['=>' => 'align_single_space_minimal'],
        ],
        'array_syntax'                          => ['syntax' => 'short'],
        'header_comment'                        => ['header' => $fileHeaderComment, 'separate' => 'both'],
        'linebreak_after_opening_tag'           => true,
        'mb_str_functions'                      => true,
        'no_php4_constructor'                   => true,
        'no_superfluous_phpdoc_tags'            => true,
        'no_unreachable_default_argument_value' => true,
        'no_useless_else'                       => true,
        'no_useless_return'                     => true,
        'ordered_imports'                       => true,
        'php_unit_strict'                       => false,
        'phpdoc_order'                          => true,
        'semicolon_after_instruction'           => true,
        'strict_comparison'                     => true,
        'strict_param'                          => true,
        'trailing_comma_in_multiline'           => true,
        'php_unit_method_casing'                => false,
        'php_unit_test_annotation'              => false,
        'visibility_required'                   => false,
    ])
    ->setFinder($finder)
    ->setCacheFile(__DIR__.'/.php_cs.cache')
;
