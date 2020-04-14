<?php
declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit;

use HTMLPurifier_Config;
use HTMLPurifier\DefinitionCache\DevNull;
use Mockery;

/**
 * Class DefinitionCacheTest
 *
 * @package HTMLPurifier\Tests\Unit
 */
class DefinitionCacheTest extends TestCase
{
    /**
     * @test
     */
    public function testParseCDATA(): void
    {
        // using null subclass because parent is abstract
        $cache = new DevNull('Test');

        $config = Mockery::mock(HTMLPurifier_Config::class)->makePartial();
        $config->version = '1.0.0'; // hopefully no conflicts

        $config->expects()
            ->get('Test.DefinitionRev')
            ->times(2)
            ->andReturns(10);

        $config->expects()
            ->getBatchSerial('Test')
            ->times(3)
            ->andReturns('hash');

        static::assertEquals(false, $cache->isOld('1.0.0,hash,10', $config));
        static::assertEquals(true, $cache->isOld('1.5.0,hash,1', $config));

        static::assertEquals(true, $cache->isOld('0.9.0,hash,1', $config));
        static::assertEquals(true, $cache->isOld('1.0.0,hash,1', $config));
        static::assertEquals(true, $cache->isOld('1.0.0beta,hash,11', $config));

        static::assertEquals(true, $cache->isOld('0.9.0,hash2,1', $config));
        static::assertEquals(false, $cache->isOld('1.0.0,hash2,1', $config)); // if hash is different, don't touch!
        static::assertEquals(true, $cache->isOld('1.0.0beta,hash2,11', $config));
        static::assertEquals(true, $cache->isOld('1.0.0-dev,hash2,11', $config));
    }
}
