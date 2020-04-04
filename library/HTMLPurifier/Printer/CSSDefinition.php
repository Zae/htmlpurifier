<?php

declare(strict_types=1);

/**
 * Class HTMLPurifier_Printer_CSSDefinition
 */
class HTMLPurifier_Printer_CSSDefinition extends HTMLPurifier_Printer
{
    /**
     * @type HTMLPurifier_CSSDefinition
     */
    protected $def;

    /**
     * @param HTMLPurifier_Config $config
     *
     * @return string
     * @throws HTMLPurifier_Exception
     */
    public function render(HTMLPurifier_Config $config): string
    {
        $this->def = $config->getCSSDefinition();
        $ret = '';

        $ret .= $this->start('div', ['class' => 'HTMLPurifier_Printer']);
        $ret .= $this->start('table');

        $ret .= $this->element('caption', 'Properties ($info)');

        $ret .= $this->start('thead');
        $ret .= $this->start('tr');
        $ret .= $this->element('th', 'Property', ['class' => 'heavy']);
        $ret .= $this->element('th', 'Definition', ['class' => 'heavy', 'style' => 'width:auto;']);
        $ret .= $this->end('tr');
        $ret .= $this->end('thead');

        ksort($this->def->info);
        foreach ($this->def->info as $property => $obj) {
            $name = $this->getClass($obj, 'AttrDef_');
            $ret .= $this->row($property, $name);
        }

        $ret .= $this->end('table');
        return $ret . $this->end('div');
    }
}
