<?php

declare(strict_types=1);

// must be called POST validation
namespace HTMLPurifier\AttrTransform;

use HTMLPurifier\Context;
use HTMLPurifier\AttrTransform;
use HTMLPurifier\URIParser;
use \HTMLPurifier\Config;
use HTMLPurifier\Exception;

/**
 * Adds rel="nofollow" to all outbound links.  This transform is
 * only attached if Attr.Nofollow is TRUE.
 */
class Nofollow extends AttrTransform
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
     * @param \HTMLPurifier\Config $config
     * @param Context             $context
     *
     * @return array
     * @throws \HTMLPurifier\Exception
     */
    public function transform(array $attr, \HTMLPurifier\Config $config, Context $context): array
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
