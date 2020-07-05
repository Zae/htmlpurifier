<?php

declare(strict_types=1);

/* W3C says:
    [ // adjective and number must be in correct order, even if
      // you could switch them without introducing ambiguity.
      // some browsers support that syntax
        [
            <percentage> | <length> | left | center | right
        ]
        [
            <percentage> | <length> | top | center | bottom
        ]?
    ] |
    [ // this signifies that the vertical and horizontal adjectives
      // can be arbitrarily ordered, however, there can only be two,
      // one of each, or none at all
        [
            left | center | right
        ] ||
        [
            top | center | bottom
        ]
    ]
    top, left = 0%
    center, (none) = 50%
    bottom, right = 100%
*/

/* QuirksMode says:
    keyword + length/percentage must be ordered correctly, as per W3C

    Internet Explorer and Opera, however, support arbitrary ordering. We
    should fix it up.

    Minor issue though, not strictly necessary.
*/

// control freaks may appreciate the ability to convert these to
// percentages or something, but it's not necessary
namespace HTMLPurifier\AttrDef\CSS;

use HTMLPurifier\AttrDef;
use HTMLPurifier\Config;
use HTMLPurifier\Context;

use function count;

/**
 * Validates the value of background-position.
 */
class BackgroundPosition extends AttrDef
{
    /**
     * @type Length
     */
    protected $length;

    /**
     * @type Percentage
     */
    protected $percentage;

    public function __construct()
    {
        $this->length = new Length();
        $this->percentage = new Percentage();
    }

    /**
     * @param string       $string
     * @param Config|null  $config
     * @param Context|null $context
     *
     * @return bool|string
     */
    public function validate(string $string, ?Config $config, ?Context $context)
    {
        $string = $this->parseCDATA($string);
        $bits = explode(' ', $string);

        $keywords = [];
        $keywords['h'] = false; // left, right
        $keywords['v'] = false; // top, bottom
        $keywords['ch'] = false; // center (first word)
        $keywords['cv'] = false; // center (second word)
        $measures = [];

        $i = 0;

        $lookup = [
            'top' => 'v',
            'bottom' => 'v',
            'left' => 'h',
            'right' => 'h',
            'center' => 'c'
        ];

        foreach ($bits as $bit) {
            if ($bit === '') {
                continue;
            }

            // test for keyword
            $lbit = ctype_lower($bit) ? $bit : strtolower($bit);
            if (isset($lookup[$lbit])) {
                $status = $lookup[$lbit];
                if ($status === 'c') {
                    if ($i === 0) {
                        $status = 'ch';
                    } else {
                        $status = 'cv';
                    }
                }

                $keywords[$status] = $lbit;
                $i++;
            }

            // test for length
            $r = $this->length->validate($bit, $config, $context);
            if ($r !== false) {
                $measures[] = $r;
                $i++;
            }

            // test for percentage
            $r = $this->percentage->validate($bit, $config, $context);
            if ($r !== false) {
                $measures[] = $r;
                $i++;
            }
        }

        if (!$i) {
            return false;
        } // no valid values were caught

        $ret = [];

        // first keyword
        if ($keywords['h']) {
            $ret[] = $keywords['h'];
        } elseif ($keywords['ch']) {
            $ret[] = $keywords['ch'];
            $keywords['cv'] = false; // prevent re-use: center = center center
        } elseif (count($measures)) {
            $ret[] = array_shift($measures);
        }

        if ($keywords['v']) {
            $ret[] = $keywords['v'];
        } elseif ($keywords['cv']) {
            $ret[] = $keywords['cv'];
        } elseif (count($measures)) {
            $ret[] = array_shift($measures);
        }

        if (empty($ret)) {
            return false;
        }

        return implode(' ', $ret);
    }
}
