<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit\AttrTransform;

use HTMLPurifier\AttrTransform\BdoDir;

/**
 * Class BdoDirTest
 *
 * @package HTMLPurifier\Tests\Unit\AttrTransform
 */
class BdoDirTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->obj = new BdoDir();
    }

    /**
     * @test
     */
    public function testAddDefaultDir(): void
    {
        $this->assertResult([], ['dir' => 'ltr']);
    }

    /**
     * @test
     */
    public function testPreserveExistingDir(): void
    {
        $this->assertResult(['dir' => 'rtl']);
    }

    /**
     * @test
     */
    public function testAlternateDefault(): void
    {
        $this->config->set('Attr.DefaultTextDir', 'rtl');
        $this->assertResult(
            [],
            ['dir' => 'rtl']
        );
    }
}
