<?php

declare(strict_types=1);

namespace HTMLPurifier\HTMLModule;

use HTMLPurifier\ChildDef\Table;
use HTMLPurifier\HTMLModule;
use HTMLPurifier\Config;

/**
 * XHTML 1.1 Tables Module, fully defines accessible table elements.
 */
class Tables extends HTMLModule
{
    /**
     * @type string
     */
    public $name = 'Tables';

    /**
     * @param Config $config
     */
    public function setup(Config $config): void
    {
        $this->addElement('caption', null, 'Inline', 'Common');

        $this->addElement(
            'table',
            'Block',
            new Table(),
            'Common',
            [
                'border' => 'Pixels',
                'cellpadding' => 'Length',
                'cellspacing' => 'Length',
                'frame' => 'Enum#void,above,below,hsides,lhs,rhs,vsides,box,border',
                'rules' => 'Enum#none,groups,rows,cols,all',
                'summary' => 'Text',
                'width' => 'Length'
            ]
        );

        // common attributes
        $cell_align = [
            'align' => 'Enum#left,center,right,justify,char',
            'charoff' => 'Length',
            'valign' => 'Enum#top,middle,bottom,baseline',
        ];

        $cell_t = array_merge(
            [
                'abbr' => 'Text',
                'colspan' => 'Number',
                'rowspan' => 'Number',
                // Apparently, as of HTML5 this attribute only applies
                // to 'th' elements.
                'scope' => 'Enum#row,col,rowgroup,colgroup',
            ],
            $cell_align
        );
        $this->addElement('td', null, 'Flow', 'Common', $cell_t);
        $this->addElement('th', null, 'Flow', 'Common', $cell_t);

        $this->addElement('tr', null, 'Required: td | th', 'Common', $cell_align);

        $cell_col = array_merge(
            [
                'span' => 'Number',
                'width' => 'MultiLength',
            ],
            $cell_align
        );
        $this->addElement('col', null, 'Empty', 'Common', $cell_col);
        $this->addElement('colgroup', null, 'Optional: col', 'Common', $cell_col);

        $this->addElement('tbody', null, 'Required: tr', 'Common', $cell_align);
        $this->addElement('thead', null, 'Required: tr', 'Common', $cell_align);
        $this->addElement('tfoot', null, 'Required: tr', 'Common', $cell_align);
    }
}
