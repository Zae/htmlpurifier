<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit;

use HTMLPurifier\Token\Start;

/**
 * Class TokenTest
 *
 * @package HTMLPurifier\Tests\Unit
 */
class TokenTest extends TestCase
{
    /**
     * @param      $name
     * @param      $attr
     * @param null $expect_name
     * @param null $expect_attr
     */
    private function assertTokenConstruction(
        $name,
        $attr,
        $expect_name = null,
        $expect_attr = null
    ): void {
        if ($expect_name === null) {
            $expect_name = $name;
        }

        if ($expect_attr === null) {
            $expect_attr = $attr;
        }

        $token = new Start($name, $attr);

        static::assertEquals($expect_name, $token->name);
        static::assertEquals($expect_attr, $token->attr);
    }

    /**
     * @test
     */
    public function testConstruct(): void
    {
        // standard case
        $this->assertTokenConstruction('a', ['href' => 'about:blank']);

        // lowercase the tag's name
        $this->assertTokenConstruction('A', ['href' => 'about:blank'],
            'a');

        // lowercase attributes
        $this->assertTokenConstruction(
            'a',
            ['HREF' => 'about:blank'],
            'a',
            ['href' => 'about:blank']
        );
    }
}
