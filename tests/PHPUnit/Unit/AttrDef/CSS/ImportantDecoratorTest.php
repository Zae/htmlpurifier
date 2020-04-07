<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit\AttrDef\CSS;

use HTMLPurifier\AttrDef;
use HTMLPurifier\AttrDef\CSS\ImportantDecorator;
use HTMLPurifier\Tests\Unit\AttrDef\TestCase;
use Mockery;

/**
 * Class ImportantDecoratorTest
 *
 * @package HTMLPurifier\Tests\Unit\AttrDef\CSS
 */
class ImportantDecoratorTest extends TestCase
{
    /**
     * @var AttrDef|Mockery\LegacyMockInterface|Mockery\MockInterface
     */
    private $mock;

    public function setUp(): void
    {
        $this->mock = Mockery::mock(AttrDef::class);
        $this->def  = new ImportantDecorator($this->mock, true);
    }

    /**
     * @test
     */
    public function testImportant(): void
    {
        $this->setMock('23');
        $this->assertDef('23 !important');
    }

    /**
     * @test
     */
    public function testImportantInternalDefChanged(): void
    {
        $this->setMock('23', '24');
        $this->assertDef('23 !important', '24 !important');
    }

    /**
     * @test
     */
    public function testImportantWithSpace(): void
    {
        $this->setMock('23');
        $this->assertDef('23 !  important  ', '23 !important');
    }

    /**
     * @test
     */
    public function testFakeImportant(): void
    {
        $this->setMock('! foo important');
        $this->assertDef('! foo important');
    }

    /**
     * @test
     */
    public function testStrip(): void
    {
        $this->def  = new ImportantDecorator($this->mock, false);
        $this->setMock('23');
        $this->assertDef('23 !  important  ', '23');
    }

    /**
     * @param      $input
     * @param null $output
     */
    private function setMock($input, $output = null): void
    {
        if ($output === null) {
            $output = $input;
        }

        $this->mock->expects()
            ->validate($input, $this->config, $this->context)
            ->once()
            ->andReturn($output);
    }
}
