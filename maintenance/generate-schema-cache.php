#!/usr/bin/php
<?php

declare(strict_types=1);

use HTMLPurifier\ConfigSchema\Builder\ConfigSchema;
use HTMLPurifier\ConfigSchema\InterchangeBuilder;
use HTMLPurifier\ConfigSchema\Interchange;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/common.php';

assertCli();

/**
 * @file
 * Generates a schema cache file, saving it to
 * library/HTMLPurifier/ConfigSchema/schema.ser.
 *
 * This should be run when new configuration options are added to
 * HTML Purifier. A cached version is available via the repository
 * so this does not normally have to be regenerated.
 *
 * If you have a directory containing custom configuration schema files,
 * you can simple add a path to that directory as a parameter to
 * this, and they will get included.
 */

$target = __DIR__ . '/../src/ConfigSchema/schema.ser';

$builder = new InterchangeBuilder();
$interchange = new Interchange();

$builder->buildDir($interchange);

$loader = __DIR__ . '/../config-schema.php';
if (file_exists($loader)) {
    include $loader;
}
foreach ($_SERVER['argv'] as $i => $dir) {
    if ($i === 0) {
        continue;
    }
    $builder->buildDir($interchange, realpath($dir));
}

$interchange->validate();

$schema_builder = new ConfigSchema();
$schema = $schema_builder->build($interchange);

echo "Saving schema... ";
file_put_contents($target, serialize($schema));
echo "done!\n";
