<?php

use HTMLPurifier\LanguageFactory;

class HTMLPurifier_LanguageFactoryTest extends HTMLPurifier_Harness
{

    /**
     * Protected reference of global factory we're testing.
     */
    protected $factory;

    public function setUp()
    {
        $this->factory = LanguageFactory::instance();
        parent::setUp();
    }

    public function test()
    {
        $this->config->set('Core.Language', 'en');
        $language = $this->factory->create($this->config, $this->context);

        $this->assertIsA($language, 'HTMLPurifier\Language');
        $this->assertIdentical($language->code, 'en');

        // lazy loading test
        $this->assertIdentical(count($language->messages), 0);
        $language->load();
        $this->assertNotEqual(count($language->messages), 0);

    }
}

// vim: et sw=4 sts=4
