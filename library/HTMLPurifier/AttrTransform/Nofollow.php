<?php

declare(strict_types=1);

// must be called POST validation

/**
 * Adds rel="nofollow" to all outbound links.  This transform is
 * only attached if Attr.Nofollow is TRUE.
 */
class HTMLPurifier_AttrTransform_Nofollow extends HTMLPurifier_AttrTransform
{
    /**
     * @type HTMLPurifier_URIParser
     */
    private $parser;

    public function __construct()
    {
        $this->parser = new HTMLPurifier_URIParser();
    }

    /**
     * @param array                $attr
     * @param HTMLPurifier_Config  $config
     * @param HTMLPurifier_Context $context
     *
     * @return array
     * @throws HTMLPurifier_Exception
     */
    public function transform(array $attr, HTMLPurifier_Config $config, HTMLPurifier_Context $context): array
    {
        if (!isset($attr['href'])) {
            return $attr;
        }

        // XXX Kind of inefficient
        $url = $this->parser->parse($attr['href']);
        $scheme = $url->getSchemeObj($config, $context);

        if ($scheme->browsable && !$url->isLocal($config)) {
            if (isset($attr['rel'])) {
                $rels = explode(' ', $attr['rel']);
                if (!in_array('nofollow', $rels, true)) {
                    $rels[] = 'nofollow';
                }

                $attr['rel'] = implode(' ', $rels);
            } else {
                $attr['rel'] = 'nofollow';
            }
        }

        return $attr;
    }
}
