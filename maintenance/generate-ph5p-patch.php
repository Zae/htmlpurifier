<?php

declare(strict_types=1);

/**
 * @file
 * This file compares our version of PH5P with Jero's original version, and
 * generates a patch of the differences. This script should be run whenever
 * library/HTMLPurifier/Lexer/PH5P.php is modified.
 */

$orig = realpath(__DIR__ . '/PH5P.php');
$new  = dirname(__DIR__) . '/library/HTMLPurifier/Lexer/PH5P.php';
$newt = __DIR__ . '/PH5P.new.php'; // temporary file

// minor text-processing of new file to get into same format as original
$new_src = file_get_contents($new);
$new_src = '<?php' . PHP_EOL . substr($new_src, strpos($new_src, 'class HTML5 {'));

file_put_contents($newt, $new_src);
shell_exec("diff -u \"$orig\" \"$newt\" > PH5P.patch");
unlink($newt);
