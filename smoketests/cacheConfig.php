<?php

declare(strict_types=1);

use HTMLPurifier\Config;
use HTMLPurifier\HTMLPurifier;

require_once __DIR__ . '/common.php';

$config = Config::createDefault();
$config->set('HTML.Doctype', 'HTML 4.01 Strict');
$config->set('HTML.Allowed', 'b,a[href],br');
$config->set('CSS.AllowTricky', true);
$config->set('URI.Disable', true);
$serial = $config->serialize();

$result = unserialize($serial);
$purifier = new HTMLPurifier($result);
echo htmlspecialchars($purifier->purify('<b>Bold</b><br><i><a href="http://google.com">no</a> formatting</i>'));
