<?php

declare(strict_types=1);

use HTMLPurifier\Config;
use HTMLPurifier\HTMLPurifier;

require_once __DIR__ . '/common.php';

// todo : modularize the HTML in to separate files

$allowed = [
    'allElements' => true,
    'legacy' => true
];

$page = $_GET['p'] ?? false;
if (!isset($allowed[$page])) {
    $page = false;
}

$strict = isset($_GET['d']) ? (bool) $_GET['d'] : false;

echo '<?xml version="1.0" encoding="UTF-8" ?>';
?>
<?php if ($strict) { ?>
<!DOCTYPE html
     PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
     "http://www.w3.org/TR/xhtml1/DTD/xhtml1- Strict.dtd">
<?php } else { ?>
<!DOCTYPE html
     PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
     "http://www.w3.org/TR/xhtml1/DTD/xhtml1-loose.dtd">
<?php } ?>
<html>
<head>
    <title>HTML Purifier Basic Smoketest</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<?php
    if ($page && file_exists("basic/$page.css")) {
        ?><link rel="stylesheet" href="basic/<?= $page ?>.css" type="text/css" /><?php
    }
?>
</head>
<body>
<?php

if ($page) {
?>
<div style="float:right;"><div><?= $strict ? 'Strict' : 'Loose' ?>:
<a href="?d=<?= (int)!$strict ?>&amp;p=<?= $page ?>">Swap</a></div>
<a href="http://validator.w3.org/check?uri=referer"><img
        src="http://www.w3.org/Icons/valid-xhtml10"
        alt="Valid XHTML 1.0 Transitional" height="31" width="88" style="border:0;" /></a>
</div>
<?php
    $config = Config::createDefault();
    $config->set('Attr.EnableID', true);
    $config->set('HTML.Strict', $strict);
    $purifier = new HTMLPurifier($config);
    echo $purifier->purify(file_get_contents("basic/$page.html"));
} else {
    ?>
    <h1>HTML Purifier Basic Smoketest Index</h1>
    <ul>
    <?php
    foreach ($allowed as $val => $b) {
        ?><li><a href="?p=<?= $val ?>"><?= $val ?></a></li><?php
    }
    ?></ul><?php
}

?>
</body>
</html>
