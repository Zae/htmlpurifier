<?php

declare(strict_types=1);

namespace HTMLPurifier;

use HTMLPurifier\AttrDef;
use HTMLPurifier\AttrDef\Switcher;
use HTMLPurifier\AttrDef\Integer;
use HTMLPurifier\AttrDef\Enum;
use HTMLPurifier\AttrDef\CSS\URI;
use HTMLPurifier\AttrDef\CSS\TextDecoration;
use HTMLPurifier\AttrDef\CSS\Percentage;
use HTMLPurifier\AttrDef\CSS\Number;
use HTMLPurifier\AttrDef\CSS\Multiple;
use HTMLPurifier\AttrDef\CSS\ListStyle;
use HTMLPurifier\AttrDef\CSS\Length;
use HTMLPurifier\AttrDef\CSS\FontFamily;
use HTMLPurifier\AttrDef\CSS\Font;
use HTMLPurifier\AttrDef\CSS\Filter;
use HTMLPurifier\AttrDef\CSS\Composite;
use HTMLPurifier\AttrDef\CSS\Color;
use HTMLPurifier\AttrDef\CSS\Border;
use HTMLPurifier\AttrDef\CSS\BackgroundPosition;
use HTMLPurifier\AttrDef\CSS\Background;
use HTMLPurifier\AttrDef\CSS\AlphaValue;
use HTMLPurifier\AttrDef\CSS\ImportantDecorator;
use HTMLPurifier\HTMLDefinition;
use HTMLPurifier\Definition;
use HTMLPurifier_Config;
use HTMLPurifier\Exception;

/**
 * Defines allowed CSS attributes and what their values are.
 *
 * @see HTMLDefinition
 */
class CSSDefinition extends Definition
{
    public $type = 'CSS';

    /**
     * Assoc array of attribute name to definition object.
     *
     * @type AttrDef[]
     */
    public $info = [];

    /**
     * Constructs the info array.  The meat of this class.
     *
     * @param HTMLPurifier_Config $config
     */
    protected function doSetup($config)
    {
        $this->info['text-align'] = new Enum(
            ['left', 'right', 'center', 'justify'],
            false
        );

        $border_style =
        $this->info['border-bottom-style'] =
        $this->info['border-right-style'] =
        $this->info['border-left-style'] =
        $this->info['border-top-style'] = new Enum(
            [
                'none',
                'hidden',
                'dotted',
                'dashed',
                'solid',
                'double',
                'groove',
                'ridge',
                'inset',
                'outset'
            ],
            false
        );

        $this->info['border-style'] = new Multiple($border_style);

        $this->info['clear'] = new Enum(
            ['none', 'left', 'right', 'both'],
            false
        );
        $this->info['float'] = new Enum(
            ['none', 'left', 'right'],
            false
        );
        $this->info['font-style'] = new Enum(
            ['normal', 'italic', 'oblique'],
            false
        );
        $this->info['font-variant'] = new Enum(
            ['normal', 'small-caps'],
            false
        );

        $uri_or_none = new Composite(
            [
                new Enum(['none']),
                new URI()
            ]
        );

        $this->info['list-style-position'] = new Enum(
            ['inside', 'outside'],
            false
        );

        $this->info['list-style-type'] = new Enum(
            [
                'disc',
                'circle',
                'square',
                'decimal',
                'lower-roman',
                'upper-roman',
                'lower-alpha',
                'upper-alpha',
                'none'
            ],
            false
        );

        $this->info['list-style-image'] = $uri_or_none;

        $this->info['list-style'] = new ListStyle($config);

        $this->info['text-transform'] = new Enum(
            ['capitalize', 'uppercase', 'lowercase', 'none'],
            false
        );
        $this->info['color'] = new Color();

        $this->info['background-image'] = $uri_or_none;
        $this->info['background-repeat'] = new Enum(
            ['repeat', 'repeat-x', 'repeat-y', 'no-repeat']
        );
        $this->info['background-attachment'] = new Enum(
            ['scroll', 'fixed']
        );
        $this->info['background-position'] = new BackgroundPosition();

        $border_color =
        $this->info['border-top-color'] =
        $this->info['border-bottom-color'] =
        $this->info['border-left-color'] =
        $this->info['border-right-color'] =
        $this->info['background-color'] = new Composite(
            [
                new Enum(['transparent']),
                new Color()
            ]
        );

        $this->info['background'] = new Background($config);

        $this->info['border-color'] = new Multiple($border_color);

        $border_width =
        $this->info['border-top-width'] =
        $this->info['border-bottom-width'] =
        $this->info['border-left-width'] =
        $this->info['border-right-width'] = new Composite(
            [
                new Enum(['thin', 'medium', 'thick']),
                new Length('0') //disallow negative
            ]
        );

        $this->info['border-width'] = new Multiple($border_width);

        $this->info['letter-spacing'] = new Composite(
            [
                new Enum(['normal']),
                new Length()
            ]
        );

        $this->info['word-spacing'] = new Composite(
            [
                new Enum(['normal']),
                new Length()
            ]
        );

        $this->info['font-size'] = new Composite(
            [
                new Enum(
                    [
                        'xx-small',
                        'x-small',
                        'small',
                        'medium',
                        'large',
                        'x-large',
                        'xx-large',
                        'larger',
                        'smaller'
                    ]
                ),
                new Percentage(),
                new Length()
            ]
        );

        $this->info['line-height'] = new Composite(
            [
                new Enum(['normal']),
                new Number(true), // no negatives
                new Length('0'),
                new Percentage(true)
            ]
        );

        $margin =
        $this->info['margin-top'] =
        $this->info['margin-bottom'] =
        $this->info['margin-left'] =
        $this->info['margin-right'] = new Composite(
            [
                new Length(),
                new Percentage(),
                new Enum(['auto'])
            ]
        );

        $this->info['margin'] = new Multiple($margin);

        // non-negative
        $padding =
        $this->info['padding-top'] =
        $this->info['padding-bottom'] =
        $this->info['padding-left'] =
        $this->info['padding-right'] = new Composite(
            [
                new Length('0'),
                new Percentage(true)
            ]
        );

        $this->info['padding'] = new Multiple($padding);

        $this->info['text-indent'] = new Composite(
            [
                new Length(),
                new Percentage()
            ]
        );

        $trusted_wh = new Composite(
            [
                new Length('0'),
                new Percentage(true),
                new Enum(['auto', 'initial', 'inherit'])
            ]
        );
        $trusted_min_wh = new Composite(
            [
                new Length('0'),
                new Percentage(true),
                new Enum(['initial', 'inherit'])
            ]
        );
        $trusted_max_wh = new Composite(
            [
                new Length('0'),
                new Percentage(true),
                new Enum(['none', 'initial', 'inherit'])
            ]
        );

        $max = $config->get('CSS.MaxImgLength');

        $this->info['width'] =
        $this->info['height'] =
            $max === null ?
                $trusted_wh :
                new Switcher(
                    'img',
                    // For img tags:
                    new Composite(
                        [
                            new Length('0', $max),
                            new Enum(['auto'])
                        ]
                    ),
                    // For everyone else:
                    $trusted_wh
                );

        $this->info['min-width'] =
        $this->info['min-height'] =
            $max === null ?
                $trusted_min_wh :
                new Switcher(
                    'img',
                    // For img tags:
                    new Composite(
                        [
                            new Length('0', $max),
                            new Enum(['initial', 'inherit'])
                        ]
                    ),
                    // For everyone else:
                    $trusted_min_wh
                );

        $this->info['max-width'] =
        $this->info['max-height'] =
            $max === null ?
                $trusted_max_wh :
                new Switcher(
                    'img',
                    // For img tags:
                    new Composite(
                        [
                            new Length('0', $max),
                            new Enum(['none', 'initial', 'inherit'])
                        ]
                    ),
                    // For everyone else:
                    $trusted_max_wh
                );

        $this->info['text-decoration'] = new TextDecoration();

        $this->info['font-family'] = new FontFamily();

        // this could use specialized code
        $this->info['font-weight'] = new Enum(
            [
                'normal',
                'bold',
                'bolder',
                'lighter',
                '100',
                '200',
                '300',
                '400',
                '500',
                '600',
                '700',
                '800',
                '900'
            ],
            false
        );

        // MUST be called after other font properties, as it references
        // a CSSDefinition object
        $this->info['font'] = new Font($config);

        // same here
        $this->info['border'] =
        $this->info['border-bottom'] =
        $this->info['border-top'] =
        $this->info['border-left'] =
        $this->info['border-right'] = new Border($config);

        $this->info['border-collapse'] = new Enum(
            ['collapse', 'separate']
        );

        $this->info['caption-side'] = new Enum(
            ['top', 'bottom']
        );

        $this->info['table-layout'] = new Enum(
            ['auto', 'fixed']
        );

        $this->info['vertical-align'] = new Composite(
            [
                new Enum(
                    [
                        'baseline',
                        'sub',
                        'super',
                        'top',
                        'text-top',
                        'middle',
                        'bottom',
                        'text-bottom'
                    ]
                ),
                new Length(),
                new Percentage()
            ]
        );

        $this->info['border-spacing'] = new Multiple(new Length(), 2);

        // These CSS properties don't work on many browsers, but we live
        // in THE FUTURE!
        $this->info['white-space'] = new Enum(
            ['nowrap', 'normal', 'pre', 'pre-wrap', 'pre-line']
        );

        if ($config->get('CSS.Proprietary')) {
            $this->doSetupProprietary($config);
        }

        if ($config->get('CSS.AllowTricky')) {
            $this->doSetupTricky($config);
        }

        if ($config->get('CSS.Trusted')) {
            $this->doSetupTrusted($config);
        }

        $allow_important = $config->get('CSS.AllowImportant');
        // wrap all attr-defs with decorator that handles !important
        foreach ($this->info as $k => $v) {
            $this->info[$k] = new ImportantDecorator($v, $allow_important);
        }

        $this->setupConfigStuff($config);
    }

    /**
     * @param HTMLPurifier_Config $config
     */
    protected function doSetupProprietary(HTMLPurifier_Config $config)
    {
        // Internet Explorer only scrollbar colors
        $this->info['scrollbar-arrow-color'] = new Color();
        $this->info['scrollbar-base-color'] = new Color();
        $this->info['scrollbar-darkshadow-color'] = new Color();
        $this->info['scrollbar-face-color'] = new Color();
        $this->info['scrollbar-highlight-color'] = new Color();
        $this->info['scrollbar-shadow-color'] = new Color();

        // vendor specific prefixes of opacity
        $this->info['-moz-opacity'] = new AlphaValue();
        $this->info['-khtml-opacity'] = new AlphaValue();

        // only opacity, for now
        $this->info['filter'] = new Filter();

        // more CSS3
        $this->info['page-break-after'] =
        $this->info['page-break-before'] = new Enum(
            [
                'auto',
                'always',
                'avoid',
                'left',
                'right'
            ]
        );
        $this->info['page-break-inside'] = new Enum(['auto', 'avoid']);

        $border_radius = new Composite(
            [
                new Percentage(true), // disallow negative
                new Length('0') // disallow negative
            ]);

        $this->info['border-top-left-radius'] =
        $this->info['border-top-right-radius'] =
        $this->info['border-bottom-right-radius'] =
        $this->info['border-bottom-left-radius'] = new Multiple($border_radius, 2);
        // TODO: support SLASH syntax
        $this->info['border-radius'] = new Multiple($border_radius, 4);

    }

    /**
     * @param HTMLPurifier_Config $config
     */
    protected function doSetupTricky($config)
    {
        $this->info['display'] = new Enum(
            [
                'inline',
                'block',
                'list-item',
                'run-in',
                'compact',
                'marker',
                'table',
                'inline-block',
                'inline-table',
                'table-row-group',
                'table-header-group',
                'table-footer-group',
                'table-row',
                'table-column-group',
                'table-column',
                'table-cell',
                'table-caption',
                'none'
            ]
        );

        $this->info['visibility'] = new Enum(
            ['visible', 'hidden', 'collapse']
        );
        $this->info['overflow'] = new Enum(['visible', 'hidden', 'auto', 'scroll']);
        $this->info['opacity'] = new AlphaValue();
    }

    /**
     * @param HTMLPurifier_Config $config
     */
    protected function doSetupTrusted($config)
    {
        $this->info['position'] = new Enum(
            ['static', 'relative', 'absolute', 'fixed']
        );
        $this->info['top'] =
        $this->info['left'] =
        $this->info['right'] =
        $this->info['bottom'] = new Composite(
            [
                new Length(),
                new Percentage(),
                new Enum(['auto']),
            ]
        );

        $this->info['z-index'] = new Composite(
            [
                new Integer(),
                new Enum(['auto']),
            ]
        );
    }

    /**
     * Performs extra config-based processing. Based off of
     * HTMLPurifier\HTMLPurifier_HTMLDefinition.
     *
     * @param HTMLPurifier_Config $config
     *
     * @throws Exception
     * @todo Refactor duplicate elements into common class (probably using composition, not inheritance).
     */
    protected function setupConfigStuff($config): void
    {
        // setup allowed elements
        $support = '(for information on implementing this, see the support forums) ';
        $allowed_properties = $config->get('CSS.AllowedProperties');
        if ($allowed_properties !== null) {
            foreach ($this->info as $name => $d) {
                if (!isset($allowed_properties[$name])) {
                    unset($this->info[$name]);
                }
                unset($allowed_properties[$name]);
            }
            // emit errors
            foreach ($allowed_properties as $name => $d) {
                // :TODO: Is this htmlspecialchars() call really necessary?
                $name = htmlspecialchars($name);
                trigger_error("Style attribute '$name' is not supported $support", E_USER_WARNING);
            }
        }

        $forbidden_properties = $config->get('CSS.ForbiddenProperties');
        if ($forbidden_properties !== null) {
            foreach ($this->info as $name => $d) {
                if (isset($forbidden_properties[$name])) {
                    unset($this->info[$name]);
                }
            }
        }
    }
}
