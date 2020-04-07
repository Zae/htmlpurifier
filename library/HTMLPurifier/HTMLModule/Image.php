<?php

declare(strict_types=1);

use HTMLPurifier\AttrDef\URI;

/**
 * XHTML 1.1 Image Module provides basic image embedding.
 *
 * @note There is specialized code for removing empty images in
 *       HTMLPurifier_Strategy_RemoveForeignElements
 */
class HTMLPurifier_HTMLModule_Image extends HTMLPurifier_HTMLModule
{
    /**
     * @type string
     */
    public $name = 'Image';

    /**
     * @param HTMLPurifier_Config $config
     *
     * @throws HTMLPurifier_Exception
     */
    public function setup($config): void
    {
        $max = $config->get('HTML.MaxImgLength');
        $img = $this->addElement(
            'img',
            'Inline',
            'Empty',
            'Common',
            [
                'alt*' => 'Text',
                // According to the spec, it's Length, but percents can
                // be abused, so we allow only Pixels.
                'height' => 'Pixels#' . $max,
                'width' => 'Pixels#' . $max,
                'longdesc' => 'URI',
                'src*' => new URI(true), // embedded
            ]
        );

        if ($max === null || $config->get('HTML.Trusted')) {
            $img->attr['height'] = $img->attr['width'] = 'Length';
        }

        // kind of strange, but splitting things up would be inefficient
        $img->attr_transform_pre[] =
        $img->attr_transform_post[] =
            new HTMLPurifier_AttrTransform_ImgRequired();
    }
}
