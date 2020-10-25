<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit\DefinitionCache;

use HTMLPurifier\DefinitionCache\Serializer;

/**
 * Class SerializerTest
 *
 * @package HTMLPurifier\Tests\Unit\DefinitionCache
 */
class SerializerTest extends TestCase
{
    /**
     * @test
     * @group abc
     */
    public function test(): void
    {
        // XXX SimpleTest does some really crazy stuff in the background
        // to do equality checks. Unfortunately, this makes some
        // versions of PHP segfault. So we need to define a better,
        // homebrew notion of equality and use that instead.  For now,
        // the identical asserts are commented out.

        $cache = new Serializer('Test');

        $config = $this->generateConfigMock('serial');

        $config->shouldReceive('get')
            ->with('Test.DefinitionRev')
            ->atLeast()
            ->once()
            ->andReturn(2);

        $config->version = '1.0.0';

        $config_md5   = '1.0.0,serial,2';

        $file = $rel_file = HTMLPURIFIER_PREFIX . '/../cache/Test/' . $config_md5 . '.ser';

        // prevent previous failures from causing problems
        if ($file && file_exists($file)) {
            unlink($file);
        }

        static::assertEquals($config_md5, $cache->generateKey($config));

        $def_original = $this->generateSerializableDefinition();

        $cache->add($def_original, $config);
        static::assertFileExist($rel_file);

        $file_generated = $cache->generateFilePath($config);
        static::assertEquals(realpath($rel_file), realpath($file_generated));

        $def_1 = $cache->get($config);
        static::assertEquals($def_original, $def_1);

        $def_original->info_random = 'changed';

        $cache->set($def_original, $config);
        $def_2 = $cache->get($config);

        static::assertEquals($def_original, $def_2);
        static::assertNotEquals($def_original, $def_1);

        $def_original->info_random = 'did it change?';

        static::assertFalse($cache->add($def_original, $config));
        $def_3 = $cache->get($config);

        static::assertNotEquals($def_original, $def_3); // did not change!
        static::assertEquals($def_3, $def_2);

        $cache->replace($def_original, $config);
        $def_4 = $cache->get($config);

        static::assertEquals($def_original, $def_4);

        $cache->remove($config);
        static::assertFileDoesNotExist($file);

        static::assertFalse($cache->replace($def_original, $config));
        $def_5 = $cache->get($config);
        static::assertFalse($def_5);
    }

    /**
     * @test
     */
    public function test_errors(): void
    {
        $cache = new Serializer('Test');
        $def = $this->generateDefinition();
        $def->setup = true;
        $def->type = 'NotTest';
        $config = $this->generateConfigMock('testfoo');

        $this->expectError();
        $this->expectErrorMessage('Cannot use definition of type NotTest in cache for Test');
        $cache->add($def, $config);

        $this->expectError();
        $this->expectErrorMessage('Cannot use definition of type NotTest in cache for Test');
        $cache->set($def, $config);

        $this->expectError();
        $this->expectErrorMessage('Cannot use definition of type NotTest in cache for Test');
        $cache->replace($def, $config);
    }

    /**
     * @test
     */
    public function test_flush(): void
    {
        $cache = new Serializer('Test');

        $config1 = $this->generateConfigMock('test1');
        $config2 = $this->generateConfigMock('test2');
        $config3 = $this->generateConfigMock('test3');

        $def1 = $this->generateSerializableDefinition(['info_candles' => 1]);
        $def2 = $this->generateSerializableDefinition(['info_candles' => 2]);
        $def3 = $this->generateSerializableDefinition(['info_candles' => 3]);

        $cache->add($def1, $config1);
        $cache->add($def2, $config2);
        $cache->add($def3, $config3);

        static::assertEquals($def1, $cache->get($config1));
        static::assertEquals($def2, $cache->get($config2));
        static::assertEquals($def3, $cache->get($config3));

        $cache->flush($config1); // only essential directive is %Cache.SerializerPath

        static::assertFalse($cache->get($config1));
        static::assertFalse($cache->get($config2));
        static::assertFalse($cache->get($config3));

    }

    /**
     * @test
     * @throws \HTMLPurifier\Exception
     */
    public function testCleanup(): void
    {
        $cache = new Serializer('Test');

        // in order of age, oldest first
        // note that configurations are all identical, but version/revision
        // are different

        $config1 = $this->generateConfigMock();
        $config1->version = '0.9.0';

        $config1->shouldReceive('get')
            ->with('Test.DefinitionRev')
            ->atLeast()
            ->once()
            ->andReturn(574);

        $def1 = $this->generateSerializableDefinition(['info' => 1]);

        $config2 = $this->generateConfigMock();
        $config2->version = '1.0.0beta';
        $config2->expects()
            ->get('Test.DefinitionRev')
            ->andReturn(1);

        $def2 = $this->generateDefinition(['info' => 3]);

        $cache->set($def1, $config1);
        $cache->cleanup($config1);
        static::assertEquals($def1, $cache->get($config1)); // no change

        $cache->cleanup($config2);
        static::assertFalse($cache->get($config1));
        static::assertFalse($cache->get($config2));

    }

    /**
     * @test
     * @throws \HTMLPurifier\Exception
     */
    public function testCleanupOnlySameID(): void
    {
        $cache = new Serializer('Test');

        $config1 = $this->generateConfigMock('serial1');
        $config1->version = '1.0.0';
        $config1->shouldReceive('get')
            ->with('Test.DefinitionRev')
            ->atLeast()
            ->once()
            ->andReturn(1);

        $def1 = $this->generateSerializableDefinition(['info' => 1]);

        $config2 = $this->generateConfigMock('serial2');
        $config2->version = '1.0.0';
        $config2->shouldReceive('get')
            ->with('Test.DefinitionRev')
            ->atLeast()
            ->once()
            ->andReturn(34);

        $def2 = $this->generateSerializableDefinition(['info' => 3]);

        $cache->set($def1, $config1);
        $cache->cleanup($config1);
        static::assertEquals($def1, $cache->get($config1)); // no change

        $cache->set($def2, $config2);
        $cache->cleanup($config2);
        static::assertEquals($def1, $cache->get($config1));
        static::assertEquals($def2, $cache->get($config2));

        $cache->flush($config1);
    }

    /**
     * @test
     */
    public function testAlternatePath(): void
    {
        $cache = new Serializer('Test');
        $config = $this->generateConfigMock('serial');
        $config->version = '1.0.0';
        $config->shouldReceive('get')
            ->with('Test.DefinitionRev')
            ->atLeast()
            ->once()
            ->andReturn(1);

        $dir = __DIR__ . '/../../../tmp';
        $config->shouldReceive('get')
            ->with('Cache.SerializerPath')
            ->atLeast()
            ->once()
            ->andReturn($dir);

        $def_original = $this->generateSerializableDefinition();
        $cache->add($def_original, $config);
        static::assertFileExist($dir . '/Test/1.0.0,serial,1.ser');

        unlink($dir . '/Test/1.0.0,serial,1.ser');
        rmdir($dir . '/Test');
    }

    /**
     * @test
     */
    public function testAlternatePermissions(): void
    {
        static::markTestSkipped('Not allowed to serialize Mockery Object, need to find another way');

        $cache = new Serializer('Test');
        $config = $this->generateConfigMock('serial');
        $config->version = '1.0.0';
        $config->expects()
            ->get('Test.DefinitionRev')
            ->andReturn(1);

        $dir = __DIR__ . '/SerializerTest';
        $config->expects()
            ->get('Cache.SerializerPath')
            ->andReturn($dir);

        $config->expects()
            ->get('Cache.SerializerPermissions')
            ->andReturn(0700);

        $def_original = $this->generateDefinition();
        $cache->add($def_original, $config);
        static::assertFileExist($dir . '/Test/1.0.0,serial,1.ser');

        static::assertEquals(0600, 0777 & fileperms($dir . '/Test/1.0.0,serial,1.ser'));
        static::assertEquals(0700, 0777 & fileperms($dir . '/Test'));

        unlink($dir . '/Test/1.0.0,serial,1.ser');
        rmdir( $dir . '/Test');
    }

    /**
     * @test
     * @throws \HTMLPurifier\Exception
     */
    public function testNoInfiniteLoop(): void
    {
        $this->markAsRisky();

        $cache = new Serializer('Test');

        $config = $this->generateConfigMock('serial');
        $config->version = '1.0.0';
        $config->expects()
            ->get('Test.DefinitionRev')
            ->times(0)
            ->andReturn(1);

        $dir = __DIR__ . '/SerializerTest';
        @mkdir($dir, 0777, true);
        $config->shouldReceive('get')
            ->with('Cache.SerializerPath')
            ->atLeast()
            ->twice()
            ->andReturn($dir);

        $config->expects()
            ->get('Cache.SerializerPermissions')
            ->times(1)
            ->andReturn(0400);

        $cache->cleanup($config);
    }

    /**
     * Asserts that a file exists, ignoring the stat cache
     *
     * @param string $file
     * @param string $message
     */
    public static function assertFileExist(string $file, string $message = ''): void
    {
        clearstatcache();
        parent::assertFileExists($file, 'Expected ' . $file . ' exists');
    }

    /**
     * Asserts that a file does not exist, ignoring the stat cache
     *
     * @param string $file
     * @param string $message
     */
    public static function assertFileDoesNotExist(string $file, string $message = ''): void
    {
        clearstatcache();

        if (method_exists(parent::class, 'assertFileDoesNotExist')) {
            parent::assertFileDoesNotExist($file, "Expected {$file} does not exist");
        } else {
            parent::assertFileNotExists($file, "Expected {$file} does not exist");
        }
    }
}
