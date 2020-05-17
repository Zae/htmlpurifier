<?php

declare(strict_types=1);

namespace HTMLPurifier\HTMLModule;

use HTMLPurifier\HTMLModule;
use HTMLPurifier\Config;

/**
 * XHTML 1.1 List Module, defines list-oriented elements. Core Module.
 */
class Lists extends HTMLModule
{
    /**
     * @type string
     */
    public $name = 'Lists';

    // According to the abstract schema, the List content set is a fully formed
    // one or more expr, but it invariably occurs in an optional declaration
    // so we're not going to do that subtlety. It might cause trouble
    // if a user defines "List" and expects that multiple lists are
    // allowed to be specified, but then again, that's not very intuitive.
    // Furthermore, the actual XML Schema may disagree. Regardless,
    // we don't have support for such nested expressions without using
    // the incredibly inefficient and draconic Custom ChildDef.

    /**
     * @type array
     */
    public $content_sets = ['Flow' => 'List'];

    /**
     * @param \HTMLPurifier\Config $config
     */
    public function setup($config): void
    {
        $ol = $this->addElement('ol', 'List', new \HTMLPurifier\ChildDef\Lists(), 'Common');
        $ul = $this->addElement('ul', 'List', new \HTMLPurifier\ChildDef\Lists(), 'Common');
        // XXX The wrap attribute is handled by MakeWellFormed.  This is all
        // quite unsatisfactory, because we generated this
        // *specifically* for lists, and now a big chunk of the handling
        // is done properly by the List ChildDef.  So actually, we just
        // want enough information to make autoclosing work properly,
        // and then hand off the tricky stuff to the ChildDef.
        $ol->wrap = 'li';
        $ul->wrap = 'li';
        $this->addElement('dl', 'List', 'Required: dt | dd', 'Common');

        $this->addElement('li', null, 'Flow', 'Common');

        $this->addElement('dd', null, 'Flow', 'Common');
        $this->addElement('dt', null, 'Inline', 'Common');
    }
}
