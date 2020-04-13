<?php

declare(strict_types=1);

use HTMLPurifier\HTMLModule;

/**
 * XHTML 1.1 Ruby Annotation Module, defines elements that indicate
 * short runs of text alongside base text for annotation or pronounciation.
 */
class HTMLPurifier_HTMLModule_Ruby extends HTMLModule
{
    /**
     * @type string
     */
    public $name = 'Ruby';

    /**
     * @param HTMLPurifier_Config $config
     */
    public function setup(HTMLPurifier_Config $config): void
    {
        $this->addElement(
            'ruby',
            'Inline',
            'Custom: ((rb, (rt | (rp, rt, rp))) | (rbc, rtc, rtc?))',
            'Common'
        );
        $this->addElement('rbc', false, 'Required: rb', 'Common');
        $this->addElement('rtc', false, 'Required: rt', 'Common');
        $rb = $this->addElement('rb', false, 'Inline', 'Common');
        $rb->excludes = ['ruby' => true];
        $rt = $this->addElement('rt', false, 'Inline', 'Common', ['rbspan' => 'Number']);
        $rt->excludes = ['ruby' => true];
        $this->addElement('rp', false, 'Optional: #PCDATA', 'Common');
    }
}
