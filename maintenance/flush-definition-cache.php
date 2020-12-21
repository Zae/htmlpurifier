#!/usr/bin/php
<?php

declare(strict_types=1);

use HTMLPurifier\Config;
use HTMLPurifier\DefinitionCache\Serializer;

require_once __DIR__ . '/../vendor/autoload.php';

chdir(__DIR__);
require_once __DIR__ . '/common.php';
assertCli();

/**
 * @file
 * Flushes the definition serial cache. This file should be
 * called if changes to any subclasses of HTMLPurifier\HTMLPurifier_Definition
 * or related classes (such as HTMLPurifier\HTMLPurifier_HTMLModule) are made. This
 * may also be necessary if you've modified a customized version.
 *
 * @param Accepts one argument, cache type to flush; otherwise flushes all
 *      the caches.
 */

echo "Flushing cache... \n";

$config = Config::createDefault();

$names = ['HTML', 'CSS', 'URI', 'Test'];
if (isset($argv[1])) {
    if (in_array($argv[1], $names, true)) {
        $names = [$argv[1]];
    } else {
        throw new Exception("Cache parameter {$argv[1]} is not a valid cache");
    }
}

foreach ($names as $name) {
    echo " - Flushing $name\n";
    $cache = new Serializer($name);
    $cache->flush($config);
}

echo "Cache flushed successfully.\n";
