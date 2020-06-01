<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit;

use HTMLPurifier\Language;
use HTMLPurifier\LanguageFactory;

/**
 * Class LanguageFactoryTest
 *
 * @package HTMLPurifier\Tests\Unit
 */
class LanguageFactoryTest extends TestCase
{
    /**
     * Protected reference of global factory we're testing.
     */
    private $factory;

    protected function setUp(): void
    {
        $this->factory = LanguageFactory::instance();
        parent::setUp();
    }

    /**
     * @test
     */
    public function test(): void
    {
        $this->config->set('Core.Language', 'en');
        $language = $this->factory->create($this->config, $this->context);

        static::assertInstanceOf(Language::class, $language);
        static::assertEquals('en', $language->code);

        // lazy loading test
        static::assertCount(0, $language->messages);
        $language->load();
        static::assertNotCount(0, $language->messages);
    }

    /**
     * @test
     */
    public function testFallback(): void
    {
        if (!class_exists(Language\X::class)) {
            static::markTestSkipped('Language\X::class not loaded');
        }

        $this->config->set('Core.Language', 'x');
        $language = $this->factory->create($this->config, $this->context);

        static::assertInstanceOf(Language\X::class, $language);
        static::assertEquals('x', $language->code);

        $language->load();

        // test overloaded message
        static::assertEquals('HTML Purifier X', $language->getMessage('HTMLPurifier'));

        // test inherited message
        static::assertEquals('Pizza', $language->getMessage('LanguageFactoryTest: Pizza'));
    }

    /**
     * @test
     */
    public function testFallbackWithNoClass(): void
    {
        $this->config->set('Core.Language', 'en-x-testmini');
        $language = $this->factory->create($this->config, $this->context);

        static::assertInstanceOf( Language::class, $language);
        static::assertEquals('en-x-testmini', $language->code);
        $language->load();
        static::assertEquals('HTML Purifier XNone', $language->getMessage('HTMLPurifier'));
        static::assertEquals('Pizza', $language->getMessage('LanguageFactoryTest: Pizza'));
        static::assertEquals(false, $language->error);
    }

    /**
     * @test
     */
    public function testNoSuchLanguage(): void
    {
        $this->config->set('Core.Language', 'en-x-testnone');
        $language = $this->factory->create($this->config, $this->context);

        $this->assertInstanceOf( Language::class, $language);
        static::assertEquals('en-x-testnone', $language->code);
        static::assertEquals(true, $language->error);
    }
}
