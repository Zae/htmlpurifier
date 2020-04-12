<?php

declare(strict_types=1);

use HTMLPurifier\Context;
use HTMLPurifier\Filter;

/**
 * Class HTMLPurifier_Filter_YouTube
 */
class HTMLPurifier_Filter_YouTube extends Filter
{
    /**
     * @type string
     */
    public $name = 'YouTube';

    /**
     * @param string              $html
     * @param HTMLPurifier_Config $config
     * @param Context             $context
     *
     * @return string
     */
    public function preFilter(string $html, HTMLPurifier_Config $config, Context $context): string
    {
        $pre_regex = '#<object[^>]+>.+?' .
                     '(?:http:)?//www.youtube.com/((?:v|cp)/[A-Za-z0-9\-_=]+).+?</object>#s';
        $pre_replace = '<span class="youtube-embed">\1</span>';

        return preg_replace($pre_regex, $pre_replace, $html);
    }

    /**
     * @param string              $html
     * @param HTMLPurifier_Config $config
     * @param Context             $context
     *
     * @return string
     */
    public function postFilter(string $html, HTMLPurifier_Config $config, Context $context): string
    {
        $post_regex = '#<span class="youtube-embed">((?:v|cp)/[A-Za-z0-9\-_=]+)</span>#';

        return preg_replace_callback($post_regex, [$this, 'postFilterCallback'], $html);
    }

    /**
     * @param string $url
     *
     * @return string
     */
    protected function armorUrl(string $url): string
    {
        return str_replace('--', '-&#45;', $url);
    }

    /**
     * @param array $matches
     *
     * @return string
     */
    protected function postFilterCallback(array $matches): string
    {
        $url = $this->armorUrl($matches[1]);

        return '<object width="425" height="350" type="application/x-shockwave-flash" ' .
               'data="//www.youtube.com/' . $url . '">' .
               '<param name="movie" value="//www.youtube.com/' . $url . '"></param>' .
               '<!--[if IE]>' .
               '<embed src="//www.youtube.com/' . $url . '"' .
               'type="application/x-shockwave-flash"' .
               'wmode="transparent" width="425" height="350" />' .
               '<![endif]-->' .
               '</object>';
    }
}
