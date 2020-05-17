<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit;

use HTMLPurifier\PercentEncoder;

/**
 * Class PercentEncoderTest
 *
 * @package HTMLPurifier\Tests\Unit
 */
class PercentEncoderTest extends TestCase
{
    private $percentEncoder;
    private $func;

    public function setUp(): void
    {
        $this->percentEncoder = new PercentEncoder();
        $this->func = '';
    }

    /**
     * @param      $string
     * @param bool $expect
     */
    private function assertDecode($string, $expect = true): void
    {
        if ($expect === true) {
            $expect = $string;
        }

        static::assertEquals($expect, $this->percentEncoder->{$this->func}($string));
    }

    /**
     * @param      $string
     * @param bool $expect
     * @param bool $preserve
     */
    private function assertEncode($string, $expect = true, $preserve = false): void
    {
        if ($expect === true) {
            $expect = $string;
        }

        $encoder = new PercentEncoder($preserve);
        $result = $encoder->encode($string);

        static::assertEquals($expect, $result);
    }

    /**
     * @test
     */
    public function test_normalize(): void
    {
        $this->func = 'normalize';

        $this->assertDecode('Aw.../-$^8'); // no change
        $this->assertDecode('%41%77%7E%2D%2E%5F', 'Aw~-._'); // decode unreserved chars
        $this->assertDecode('%3A%2F%3F%23%5B%5D%40%21%24%26%27%28%29%2A%2B%2C%3B%3D'); // preserve reserved chars
        $this->assertDecode('%2b', '%2B'); // normalize to uppercase
        $this->assertDecode('%2B2B%3A3A'); // extra text
        $this->assertDecode('%2b2B%4141', '%2B2BA41'); // extra text, with normalization
        $this->assertDecode('%', '%25'); // normalize stray percent sign
        $this->assertDecode('%5%25', '%255%25'); // permaturely terminated encoding
        $this->assertDecode('%GJ', '%25GJ'); // invalid hexadecimal chars

        // contested behavior, if this changes, we'll also have to have
        // outbound encoding
        $this->assertDecode('%FC'); // not reserved or unreserved, preserve
    }

    /**
     * @test
     */
    public function test_encode_noChange(): void
    {
        $this->assertEncode('abc012-_~.');
    }

    /**
     * @test
     */
    public function test_encode_encode(): void
    {
        $this->assertEncode('>', '%3E');
    }

    /**
     * @test
     */
    public function test_encode_preserve(): void
    {
        $this->assertEncode('<>', '<%3E', '<');
    }

    /**
     * @test
     */
    public function test_encode_low(): void
    {
        $this->assertEncode("\1", '%01');
    }
}
