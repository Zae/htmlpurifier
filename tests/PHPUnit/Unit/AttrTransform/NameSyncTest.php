<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit\AttrTransform;

use HTMLPurifier\AttrTransform\NameSync;
use HTMLPurifier\IDAccumulator;

/**
 * Class NameSyncTest
 *
 * @package HTMLPurifier\Tests\Unit\AttrTransform
 */
class NameSyncTest extends TestCase
{
    /**
     * @var IDAccumulator
     */
    private $accumulator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->obj = new NameSync();
        $this->accumulator = new IDAccumulator();
        $this->context->register('IDAccumulator', $this->accumulator);
        $this->config->set('Attr.EnableID', true);
    }

    /**
     * @test
     */
    public function testEmpty(): void
    {
        $this->assertResult([]);
    }

    /**
     * @test
     */
    public function testAllowSame(): void
    {
        $this->assertResult(
            ['name' => 'free', 'id' => 'free']
        );
    }

    /**
     * @test
     */
    public function testAllowDifferent(): void
    {
        $this->assertResult(
            ['name' => 'tryit', 'id' => 'thisgood']
        );
    }

    /**
     * @test
     */
    public function testCheckName(): void
    {
        $this->accumulator->add('notok');
        $this->assertResult(
            ['name' => 'notok', 'id' => 'ok'],
            ['id' => 'ok']
        );
    }
}
