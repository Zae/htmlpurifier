<?php

declare(strict_types=1);

/**
 * @file
 * Legacy autoloader for systems lacking spl_autoload_register
 *
 */

spl_autoload_register(function($class)
{
     return HTMLPurifier_Bootstrap::autoload($class);
});

// vim: et sw=4 sts=4
