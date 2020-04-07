<?php

use HTMLPurifier\AttrDef\URI\Email\SimpleCheck;

class HTMLPurifier_AttrDef_URI_Email_SimpleCheckTest
    extends HTMLPurifier_AttrDef_URI_EmailHarness
{

    public function setUp()
    {
        $this->def = new SimpleCheck();
    }

}

// vim: et sw=4 sts=4
