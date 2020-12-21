<?php

declare(strict_types=1);

require_once __DIR__ . '/common.php';

header('Content-type: text/html; charset=UTF-8');
echo '<?xml version="1.0" encoding="UTF-8" ?>';

?><!DOCTYPE html
     PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
     "http://www.w3.org/TR/xhtml1/DTD/xhtml1-loose.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <title>HTML Purifier: All Smoketests</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <style type="text/css">
        #content {margin:5em;}
        iframe {width:100%;height:30em;}
    </style>
</head>
<body>
<h1>HTML Purifier: All Smoketests</h1>
<div id="content">
<?php

$dir = './';
$dh  = opendir($dir);
while (($filename = readdir($dh)) !== false) {
    if (
        $filename[0] === '.' ||
        $filename === 'common.php' ||
        $filename === 'all.php' ||
        $filename === 'testSchema.php' ||
        strpos($filename, '.php') === false
    ) {
        continue;
    }
    ?>
    <iframe title="" src="<?= escapeHTML($filename) ?>"></iframe>
    <?php
}

?>
</div>
</body>
</html>
