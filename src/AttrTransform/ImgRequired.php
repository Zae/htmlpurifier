<?php

declare(strict_types=1);

// must be called POST validation
namespace HTMLPurifier\AttrTransform;

use HTMLPurifier\Context;
use HTMLPurifier\AttrTransform;
use HTMLPurifier\Config;
use HTMLPurifier\Exception;

/**
 * Transform that supplies default values for the src and alt attributes
 * in img tags, as well as prevents the img tag from being removed
 * because of a missing alt tag. This needs to be registered as both
 * a pre and post attribute transform.
 */
class ImgRequired extends AttrTransform
{
    /**
     * @param array   $attr
     * @param Config  $config
     * @param Context $context
     *
     * @return array
     * @throws Exception
     */
    public function transform(array $attr, Config $config, Context $context): array
    {
        $src = true;
        if (!isset($attr['src'])) {
            if ($config->get('Core.RemoveInvalidImg')) {
                return $attr;
            }

            $attr['src'] = $config->get('Attr.DefaultInvalidImage');
            $src = false;
        }

        if (!isset($attr['alt'])) {
            if ($src) {
                $alt = $config->get('Attr.DefaultImageAlt');

                if ($alt === null) {
                    $attr['alt'] = basename($attr['src']);
                } else {
                    $attr['alt'] = $alt;
                }
            } else {
                $attr['alt'] = $config->get('Attr.DefaultInvalidImageAlt');
            }
        }

        return $attr;
    }
}
