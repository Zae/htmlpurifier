<?php

use HTMLPurifier\URIFilter\DisableResources;

class HTMLPurifier_URIFilter_DisableResourcesTest extends HTMLPurifier_URIFilterHarness
{

    public function setUp()
    {
        parent::setUp();
        $this->filter = new DisableResources();
        $var = true;
        $this->context->register('EmbeddedURI', $var);
    }

    public function testRemoveResource()
    {
        $this->assertFiltering('/foo/bar', false);
    }

    public function testPreserveRegular()
    {
        $this->context->destroy('EmbeddedURI'); // undo setUp
        $this->assertFiltering('/foo/bar');
    }

}

// vim: et sw=4 sts=4
