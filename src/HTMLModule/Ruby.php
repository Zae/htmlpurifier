<?php

declare(strict_types=1);

namespace HTMLPurifier\HTMLModule;

use HTMLPurifier\HTMLModule;
use HTMLPurifier\Config;

/**
 * XHTML 1.1 Ruby Annotation Module, defines elements that indicate
 * short runs of text alongside base text for annotation or pronounciation.
 */
class Ruby extends HTMLModule
{
    /**
     * @type string
     */
    public $name = 'Ruby';

    /**
     * @param Config $config
     */
    public function setup(Config $config): void
    {
        $this->addElement(
            'ruby',
            'Inline',
            'Custom: ((rb, (rt | (rp, rt, rp))) | (rbc, rtc, rtc?))',
            'Common'
        );
        $this->addElement('rbc', null, 'Required: rb', 'Common');
        $this->addElement('rtc', null, 'Required: rt', 'Common');
        $rb = $this->addElement('rb', null, 'Inline', 'Common');
        $rb->excludes = ['ruby' => true];
        $rt = $this->addElement('rt', null, 'Inline', 'Common', ['rbspan' => 'Number']);
        $rt->excludes = ['ruby' => true];
        $this->addElement('rp', null, 'Optional: #PCDATA', 'Common');
    }
}
