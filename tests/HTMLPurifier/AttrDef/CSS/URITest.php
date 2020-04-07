<?php

use HTMLPurifier\AttrDef\CSS\URI;

class HTMLPurifier_AttrDef_CSS_URITest extends HTMLPurifier_AttrDefHarness
{

    public function test()
    {
        $this->def = new URI();

        $this->assertDef('', false);

        // we could be nice but we won't be
        $this->assertDef('http://www.example.com/', false);

        $this->assertDef('url(', false);
        $this->assertDef('url("")', true);
        $result = 'url("http://www.example.com/")';
        $this->assertDef('url(http://www.example.com/)', $result);
        $this->assertDef('url("http://www.example.com/")', $result);
        $this->assertDef("url('http://www.example.com/')", $result);
        $this->assertDef(
            '  url(  "http://www.example.com/" )   ', $result);
        $this->assertDef("url(http://www.example.com/foo,bar\)\'\()",
            'url("http://www.example.com/foo,bar%29%27%28")');
    }

}

// vim: et sw=4 sts=4
