<?php

declare(strict_types=1);

namespace HTMLPurifier\AttrTransform;

use HTMLPurifier\AttrDef\URI;
use HTMLPurifier\AttrDef\Enum;
use HTMLPurifier\Config;
use HTMLPurifier\Context;
use HTMLPurifier\AttrTransform;
use HTMLPurifier\Exception;

/**
 * Validates name/value pairs in param tags to be used in safe objects. This
 * will only allow name values it recognizes, and pre-fill certain attributes
 * with required values.
 *
 * @note
 *      This class only supports Flash. In the future, Quicktime support
 *      may be added.
 *
 * @warning
 *      This class expects an injector to add the necessary parameters tags.
 */
class SafeParam extends AttrTransform
{
    /**
     * @var string
     */
    public $name = 'SafeParam';

    /**
     * @var URI
     */
    private $uri;

    /**
     * @var Enum
     */
    private $wmode;

    public function __construct()
    {
        $this->uri = new URI(true); // embedded
        $this->wmode = new Enum(['window', 'opaque', 'transparent']);
    }

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
        // If we add support for other objects, we'll need to alter the
        // transforms.
        switch ($attr['name']) {
            // application/x-shockwave-flash
            // Keep this synchronized with Injector/SafeObject.php
            case 'allowScriptAccess':
                $attr['value'] = 'never';
                break;
            case 'allowNetworking':
                $attr['value'] = 'internal';
                break;
            case 'allowFullScreen':
                if ($config->get('HTML.FlashAllowFullScreen')) {
                    $attr['value'] = ($attr['value'] === 'true') ? 'true' : 'false';
                } else {
                    $attr['value'] = 'false';
                }
                break;
            case 'wmode':
                $attr['value'] = $this->wmode->validate($attr['value'], $config, $context);
                break;
            case 'movie':
            case 'src':
                $attr['name'] = 'movie';
                $attr['value'] = $this->uri->validate($attr['value'], $config, $context);
                break;
            case 'flashvars':
                // we're going to allow arbitrary inputs to the SWF, on
                // the reasoning that it could only hack the SWF, not us.
                break;
                // add other cases to support other param name/value pairs
            default:
                $attr['name'] = $attr['value'] = null;
        }

        return $attr;
    }
}
