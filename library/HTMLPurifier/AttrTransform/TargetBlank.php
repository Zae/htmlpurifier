<?php

declare(strict_types=1);

// must be called POST validation
use HTMLPurifier\Context;
use HTMLPurifier\AttrTransform;
use HTMLPurifier\URIParser;

/**
 * Adds target="blank" to all outbound links.  This transform is
 * only attached if Attr.TargetBlank is TRUE.  This works regardless
 * of whether or not Attr.AllowedFrameTargets
 */
class HTMLPurifier_AttrTransform_TargetBlank extends AttrTransform
{
    /**
     * @type URIParser
     */
    private $parser;

    public function __construct()
    {
        $this->parser = new URIParser();
    }

    /**
     * @param array               $attr
     * @param HTMLPurifier_Config $config
     * @param Context             $context
     *
     * @return array
     * @throws HTMLPurifier_Exception
     */
    public function transform(array $attr, HTMLPurifier_Config $config, Context $context): array
    {
        if (!isset($attr['href'])) {
            return $attr;
        }

        // XXX Kind of inefficient
        $url = $this->parser->parse($attr['href']);
        $scheme = $url->getSchemeObj($config, $context);

        if ($scheme->browsable && !$url->isBenign($config, $context)) {
            $attr['target'] = '_blank';
        }

        return $attr;
    }
}
