<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit\AttrTransform;

use HTMLPurifier\AttrTransform\Input;

/**
 * Class InputTest
 *
 * @package HTMLPurifier\Tests\Unit\AttrTransform
 */
class InputTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->obj = new Input();
    }

    /**
     * @test
     */
    public function testEmptyInput(): void
    {
        $this->assertResult([]);
    }

    /**
     * @test
     */
    public function testInvalidCheckedWithEmpty(): void
    {
        $this->assertResult(['checked' => 'checked'], []);
    }

    /**
     * @test
     */
    public function testInvalidCheckedWithPassword(): void
    {
        $this->assertResult([
            'checked' => 'checked',
            'type' => 'password'
        ], [
            'type' => 'password'
        ]);
    }

    /**
     * @test
     */
    public function testValidCheckedWithUcCheckbox(): void
    {
        $this->assertResult([
            'checked' => 'checked',
            'type' => 'CHECKBOX',
            'value' => 'bar',
        ]);
    }

    /**
     * @test
     */
    public function testInvalidMaxlength(): void
    {
        $this->assertResult([
            'maxlength' => '10',
            'type' => 'checkbox',
            'value' => 'foo',
        ], [
            'type' => 'checkbox',
            'value' => 'foo',
        ]);
    }

    /**
     * @test
     */
    public function testValidMaxLength(): void
    {
        $this->assertResult([
            'maxlength' => '10',
        ]);
    }

    // these two are really bad test-cases

    /**
     * @test
     */
    public function testSizeWithCheckbox(): void
    {
        $this->assertResult([
            'type' => 'checkbox',
            'value' => 'foo',
            'size' => '100px',
        ], [
            'type' => 'checkbox',
            'value' => 'foo',
            'size' => '100',
        ]);
    }

    /**
     * @test
     */
    public function testSizeWithText(): void
    {
        $this->assertResult([
            'type' => 'password',
            'size' => '100px', // spurious value, to indicate no validation takes place
        ], [
            'type' => 'password',
            'size' => '100px',
        ]);
    }

    /**
     * @test
     */
    public function testInvalidSrc(): void
    {
        $this->assertResult([
            'src' => 'img.png',
        ], []);
    }

    /**
     * @test
     */
    public function testMissingValue(): void
    {
        $this->assertResult([
            'type' => 'checkbox',
        ], [
            'type' => 'checkbox',
            'value' => '',
        ]);
    }

    /**
     * @test
     * @throws \HTMLPurifier\Exception
     */
    public function testInvalidSize(): void
    {
        $this->assertResult([
            'size' => 'a',
            'type' => 'submit'
        ], [
            'type' => 'submit'
        ]);
    }
}
