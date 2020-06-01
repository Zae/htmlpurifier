<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit;

use HTMLPurifier\Config;
use HTMLPurifier\Context;
use HTMLPurifier\Generator;
use HTMLPurifier\HTMLPurifier;
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
     * @type Config
     */
    protected $config;

    /**
     * @type \HTMLPurifier\Context
     */
    protected $context;

    protected function setUp(): void
    {
        parent::setUp();

        [$this->config, $this->context] = $this->createCommon();
        $this->config->set('Output.Newline', "\n");

        $this->purifier = new HTMLPurifier();
    }

    protected function tearDown(): void
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
            Config::createDefault(),
            new Context()
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

    /**
     * Accepts config and context and prepares them into a valid state

     * @param Config &$config   Reference to config variable
     * @param Context &$context Reference to context variable
     */
    protected function prepareCommon(&$config, &$context): void
    {
        $config = Config::create($config);
        if (!$context) {
            $context = new Context();
        }
    }

    /**
     * Debugging function that prints tokens in a user-friendly manner.
     */
    protected static function printTokens($tokens, $index = null): void
    {
        $string = '<pre>';
        $generator = new Generator(Config::createDefault(), new Context());
        foreach ($tokens as $i => $token) {
            $string .= static::printToken($generator, $token, $i, $index === $i);
        }
        $string .= '</pre>';

        echo $string;
    }

    /**
     * @param $generator
     * @param $token
     * @param $i
     * @param $isCursor
     *
     * @return string
     */
    protected static function printToken($generator, $token, $i, $isCursor): string
    {
        $string = "";
        if ($isCursor) {
            $string .= '[<strong>';
        }

        $string .= "<sup>$i</sup>";
        $string .= $generator->escape($generator->generateFromToken($token));

        if ($isCursor) {
            $string .= '</strong>]';
        }

        return $string;
    }

    /**
     * Used when we want to assert that we reached the end of the test, reaching the
     * end of the test is good. This makes it so the tests don't automatically
     * become risky.
     */
    protected static function assertReached(): void
    {
        static::assertTrue(true);
    }
}
