<?php

/**
 * Generates XML and HTML documents describing configuration.
 * @note PHP 5.2+ only!
 */

/*
TODO:
- make XML format richer
- extend XSLT transformation (see the corresponding XSLT file)
- allow generation of packaged docs that can be easily moved
- multipage documentation
- determine how to multilingualize
- add blurbs to ToC
*/

use HTMLPurifier\ConfigSchema\Builder\Xml;
use HTMLPurifier\ConfigSchema\InterchangeBuilder;
use HTMLPurifier\ConfigSchema\Interchange;

require_once __DIR__ . '/../vendor/autoload.php';

error_reporting(E_ALL | E_STRICT);

// load dual-libraries
//require_once __DIR__ . '/../extras/HTMLPurifierExtras.auto.php';
//require_once dirname(__FILE__) . '/../library/HTMLPurifier.auto.php';

// setup HTML Purifier singleton
\HTMLPurifier\HTMLPurifier::getInstance([
    'AutoFormat.PurifierLinkify' => true
]);

$builder = new InterchangeBuilder();
$interchange = new Interchange();
$builder->buildDir($interchange);
$loader = __DIR__ . '/../config-schema.php';
if (file_exists($loader)) {
    include $loader;
}
$interchange->validate();

$style = 'plain'; // use $_GET in the future, careful to validate!
$configdoc_xml = __DIR__ . '/configdoc.xml';

$xml_builder = new Xml();
$xml_builder->openURI($configdoc_xml);
$xml_builder->build($interchange);
unset($xml_builder); // free handle

$xslt = new ConfigDoc_HTMLXSLTProcessor();
$xslt->importStylesheet(__DIR__ . "/styles/$style.xsl");
$output = $xslt->transformToHTML($configdoc_xml);

if (!$output) {
    echo "Error in generating files\n";
    exit(1);
}

// write out
file_put_contents(__DIR__ . "/$style.html", $output);

if (PHP_SAPI !== 'cli') {
    // output (instant feedback if it's a browser)
    echo $output;
} else {
    echo "Files generated successfully.\n";
}

// vim: et sw=4 sts=4
