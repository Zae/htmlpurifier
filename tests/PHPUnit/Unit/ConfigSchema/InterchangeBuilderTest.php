<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit\ConfigSchema;

use HTMLPurifier\ConfigSchema\Interchange;
use HTMLPurifier\ConfigSchema\InterchangeBuilder;
use HTMLPurifier\Exception;
use HTMLPurifier\StringHash;
use HTMLPurifier\Tests\Unit\TestCase;

/**
 * Class InterchangeBuilderTest
 *
 * @package HTMLPurifier\Tests\Unit\ConfigSchema
 */
class InterchangeBuilderTest extends TestCase
{
    /**
     * @test
     */
    public function testBuildFromDirectory(): void
    {
        $ic = InterchangeBuilder::buildFromDirectory(__DIR__ . '/../../files/interchangeschema');

        static::assertArrayHasKey('Attr.AllowedClasses', $ic->directives);
        static::assertCount(1, $ic->directives);
        static::assertEquals('HTML Purifier TEST', $ic->name);
    }

    /**
     * @test
     */
    public function testHashHasNoIdException(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Hash does not have any ID');

        $ib = new InterchangeBuilder();
        $ic = new Interchange();

        $ib->build($ic, new StringHash());
    }
}
