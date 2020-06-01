<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit\FSTools;

use FSTools_File;

/**
 * Class FileTest
 *
 * @package HTMLPurifier\Tests\Unit\FSTools
 */
class FileTest extends TestCase
{
    /**
     * @test
     */
    public function test(): void
    {
        $name = 'test.txt';
        $file = new FSTools_File($name);

        static::assertFalse($file->exists());

        $file->write('foobar');
        static::assertTrue($file->exists());
        static::assertEquals('foobar', $file->get());

        $file->delete();
        static::assertFalse($file->exists());
    }

    /**
     * @test
     */
    public function testGetNonExistent(): void
    {
        $name = 'notfound.txt';
        $file = new FSTools_File($name);

        $this->expectError();
        static::assertFalse($file->get());
    }

    /**
     * @test
     */
    public function testHandle(): void
    {
        $file = new FSTools_File('foo.txt');

        static::assertFalse($file->exists());

        $file->open('w');
        static::assertTrue($file->exists());

        $file->put('Foobar');
        $file->close();
        $file->open('r');

        static::assertEquals('F', $file->getChar());
        static::assertFalse($file->eof());
        static::assertEquals('oo', $file->read(2));
        static::assertEquals('bar', $file->getLine());
        static::assertTrue($file->eof());
    }
}
