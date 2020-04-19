<?php

declare(strict_types=1);

// must be called POST validation
namespace HTMLPurifier\AttrTransform;

use HTMLPurifier\Context;
use HTMLPurifier\AttrTransform;
use \HTMLPurifier\Config;

/**
 * Adds rel="noreferrer" to any links which target a different window
 * than the current one.  This is used to prevent malicious websites
 * from silently replacing the original window, which could be used
 * to do phishing.
 * This transform is controlled by %HTML.TargetNoreferrer.
 */
class TargetNoreferrer extends AttrTransform
{
    /**
     * @param array               $attr
     * @param \HTMLPurifier\Config $config
     * @param Context             $context
     *
     * @return array
     */
    public function transform(array $attr, \HTMLPurifier\Config $config, Context $context): array
    {
        if (isset($attr['rel'])) {
            $rels = explode(' ', $attr['rel']);
        } else {
            $rels = [];
        }

        if (isset($attr['target']) && !in_array('noreferrer', $rels, true)) {
            $rels[] = 'noreferrer';
        }

        if (!empty($rels) || isset($attr['rel'])) {
            $attr['rel'] = implode(' ', $rels);
        }

        return $attr;
    }
}

