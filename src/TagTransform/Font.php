<?php

declare(strict_types=1);

namespace HTMLPurifier\TagTransform;

use HTMLPurifier\Context;
use HTMLPurifier\TagTransform;
use HTMLPurifier\Token\End;
use HTMLPurifier\Token\Tag;
use \HTMLPurifier\Config;

/**
 * Transforms FONT tags to the proper form (SPAN with CSS styling)
 *
 * This transformation takes the three proprietary attributes of FONT and
 * transforms them into their corresponding CSS attributes.  These are color,
 * face, and size.
 *
 * @note    Size is an interesting case because it doesn't map cleanly to CSS.
 *       Thanks to
 *       http://style.cleverchimp.com/font_size_intervals/altintervals.html
 *       for reasonable mappings.
 * @warning This doesn't work completely correctly; specifically, this
 *          TagTransform operates before well-formedness is enforced, so
 *          the "active formatting elements" algorithm doesn't get applied.
 */
class Font extends TagTransform
{
    /**
     * @type string
     */
    public $transform_to = 'span';

    /**
     * @type array
     */
    protected $_size_lookup = [
        '-2' => '60%',
        '-1' => 'smaller',
        '0' => 'xx-small',
        '1' => 'xx-small',
        '+1' => 'larger',
        '2' => 'small',
        '+2' => '150%',
        '3' => 'medium',
        '+3' => '200%',
        '4' => 'large',
        '+4' => '300%',
        '5' => 'x-large',
        '6' => 'xx-large',
        '7' => '300%',
    ];

    /**
     * @param Tag                 $tag
     * @param \HTMLPurifier\Config $config
     * @param Context             $context
     *
     * @return End|string
     */
    public function transform(Tag $tag, \HTMLPurifier\Config $config, Context $context)
    {
        if ($tag instanceof End) {
            $new_tag = clone $tag;
            $new_tag->name = $this->transform_to;

            return $new_tag;
        }

        $attr = $tag->attr;
        $prepend_style = '';

        // handle color transform
        if (isset($attr['color'])) {
            $prepend_style .= 'color:' . $attr['color'] . ';';
            unset($attr['color']);
        }

        // handle face transform
        if (isset($attr['face'])) {
            $prepend_style .= 'font-family:' . $attr['face'] . ';';
            unset($attr['face']);
        }

        // handle size transform
        if (isset($attr['size'])) {
            // normalize large numbers
            if ($attr['size'] !== '') {
                if ($attr['size'][0] === '+' || $attr['size'][0] === '-') {
                    $size = (int)$attr['size'];
                    if ($size < -2) {
                        $attr['size'] = '-2';
                    }

                    if ($size > 4) {
                        $attr['size'] = '+4';
                    }
                } else {
                    $size = (int)$attr['size'];

                    if ($size > 7) {
                        $attr['size'] = '7';
                    }
                }
            }

            if (isset($this->_size_lookup[$attr['size']])) {
                $prepend_style .= 'font-size:' .
                                  $this->_size_lookup[$attr['size']] . ';';
            }

            unset($attr['size']);
        }

        if ($prepend_style) {
            $attr['style'] = isset($attr['style']) ?
                $prepend_style . $attr['style'] :
                $prepend_style;
        }

        $new_tag = clone $tag;
        $new_tag->name = $this->transform_to;
        $new_tag->attr = $attr;

        return $new_tag;
    }
}
