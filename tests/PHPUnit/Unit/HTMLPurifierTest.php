<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit;

use HTMLPurifier\Filter;
use HTMLPurifier\HTMLPurifier;
use Mockery;

/**
 * Class HTMLPurifierTest
 *
 * @package HTMLPurifier\Tests\Unit
 */
class HTMLPurifierTest extends TestCase
{
    protected $purifier;

    /**
     * @test
     */
    public function testNull(): void
    {
        $this->assertPurification("Null byte\0", 'Null byte');
    }

    /**
     * @test
     */
    public function test_purifyArray(): void
    {
        static::assertEquals(
            ['Good', '<b>Sketchy</b>', 'foo' => ''],
            $this->purifier->purifyArray(
                ['Good', '<b>Sketchy', 'foo' => '<script>bad</script>']
            )
        );

        static::assertIsArray($this->purifier->context);
    }

    /**
     * @test
     */
    public function test_purifyArray_nested(): void
    {
        static::assertEquals(
            ['Good', '<b>Sketchy</b>', 'foo' => ['bar' => '']],
            $this->purifier->purifyArray(
                ['Good', '<b>Sketchy', 'foo' => ['bar' => '<script>bad</script>']]
            )
        );
    }

    /**
     * @test
     */
    public function test_purifyArray_empty(): void
    {
        $purifiedEmptyArray = $this->purifier->purifyArray([]);

        static::assertEmpty($purifiedEmptyArray);
    }

    /**
     * @test
     */
    public function testGetInstance(): void
    {
        $purifier  = HTMLPurifier::getInstance();
        $purifier2 = HTMLPurifier::getInstance();

        static::assertSame($purifier, $purifier2);
    }

    /**
     * @test
     */
    public function testMakeAbsolute(): void
    {
        $this->config->set('URI.Base', 'http://example.com/bar/baz.php');
        $this->config->set('URI.MakeAbsolute', true);

        $this->assertPurification(
            '<a href="foo.txt">Foobar</a>',
            '<a href="http://example.com/bar/foo.txt">Foobar</a>'
        );
    }

    /**
     * @test
     */
    public function testDisableResources(): void
    {
        $this->config->set('URI.DisableResources', true);
        $this->assertPurification('<img src="foo.jpg" />', '');
    }

    /**
     * @test
     */
    public function test_addFilter_deprecated(): void
    {
        $this->expectError();
        $this->expectErrorMessage('HTMLPurifier->addFilter() is deprecated, use configuration directives in the Filter namespace or Filter.Custom');

        $mock = Mockery::mock(Filter::class);

        // TODO: This used to be $mock->expectOnce('preFilter'), but it seems to be called 0 times?
        $mock->expects()
            ->preFilter()
            ->never();

        // TODO: This used to be $mock->expectOnce('postFilter'), but it seems to be called 0 times?
        $mock->expects()
            ->postFilter()
            ->never();

        $this->purifier->addFilter($mock);
        $this->purifier->purify('foo');
    }
}
