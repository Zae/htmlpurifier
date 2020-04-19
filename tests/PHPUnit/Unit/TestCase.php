<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit;

use HTMLPurifier;
use HTMLPurifier_Config;
use HTMLPurifier\Context;
use Illuminate\Support\Str;
use Mockery;
use Mockery\Exception\InvalidCountException;

/**
 * Class TestCase
 *
 * @package HTMLPurifier\Tests\Unit
 */
abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    /**
     * @var HTMLPurifier
     */
    protected $purifier;

    /**
     * @type HTMLPurifier_Config
     */
    protected $config;

    /**
     * @type \HTMLPurifier\Context
     */
    protected $context;

    public function setUp(): void
    {
        parent::setUp();

        [$this->config, $this->context] = $this->createCommon();
        $this->config->set('Output.Newline', "\n");

        $this->purifier = new HTMLPurifier();
    }

    public function tearDown(): void
    {
        parent::tearDown();

        if (class_exists('Mockery')) {
            if ($container = Mockery::getContainer()) {
                $this->addToAssertionCount($container->mockery_getExpectationCount());
            }

            try {
                Mockery::close();
            } catch (InvalidCountException $e) {
                throw $e;
            }
        }
    }

    /**
     * Generates default configuration and context objects
     * @return array Defaults in form of array($config, $context)
     */
    protected function createCommon(): array
    {
        return [
            HTMLPurifier_Config::createDefault(),
            new Context
        ];
    }

    /**
     * Normalizes a string to Unix (\n) endings
     *
     * @param string $string
     */
    protected function normalize(string &$string): void
    {
        $string = str_replace(["\r\n", "\r"], "\n", $string);
    }

    /**
     * If $expect is false, ignore $result and check if status failed.
     * Otherwise, check if $status if true and $result === $expect.
     * @param bool $status status
     * @param mixed   $result result from processing
     * @param mixed   $expect expectation for result
     */
    protected function assertEitherFailOrIdentical(bool $status, $result, $expect): void
    {
        if ($expect === false) {
            static::assertFalse($status, 'Expected false result, got true');
        } else {
            static::assertTrue($status, 'Expected true result, got false');
            static::assertEquals($result, $expect);
        }
    }

    /**
     * Asserts a purification. Good for integration testing.
     *
     * @param string $input
     * @param string $expect
     *
     * @throws \HTMLPurifier\Exception
     */
    public function assertPurification(string $input, ?string $expect = null)
    {
        if ($expect === null) {
            $expect = $input;
        }

        $result = $this->purifier->purify($input, $this->config);
        static::assertEquals($expect, $result);
    }
}
