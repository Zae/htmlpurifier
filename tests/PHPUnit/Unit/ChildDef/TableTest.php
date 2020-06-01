<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit\ChildDef;

use HTMLPurifier\ChildDef\Table;

/**
 * Class TableTest
 *
 * @package HTMLPurifier\Tests\Unit\ChildDef
 */
class TableTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->obj = new Table();
    }

    /**
     * @test
     */
    public function testEmptyInput(): void
    {
        $this->assertResult('', false);
    }

    /**
     * @test
     */
    public function testSingleRow(): void
    {
        $this->assertResult('<tr />');
    }

    /**
     * @test
     */
    public function testComplexContents(): void
    {
        $this->assertResult(
            '<caption /><col /><thead /><tfoot /><tbody><tr><td>asdf</td></tr></tbody>'
        );
        $this->assertResult('<col /><col /><col /><tr />');
    }

    /**
     * @test
     */
    public function testReorderContents(): void
    {
        $this->assertResult(
            '<col /><colgroup /><tbody /><tfoot /><thead /><tr>1</tr><caption /><tr />',
            '<caption /><col /><colgroup /><thead /><tfoot /><tbody /><tbody><tr>1</tr><tr /></tbody>'
        );
    }

    /**
     * @test
     */
    public function testXhtml11Illegal(): void
    {
        $this->assertResult(
            '<thead><tr><th>a</th></tr></thead><tr><td>a</td></tr>',
            '<thead><tr><th>a</th></tr></thead><tbody><tr><td>a</td></tr></tbody>'
        );
    }

    /**
     * @test
     */
    public function testTheadOnlyNotRemoved(): void
    {
        $this->assertResult(
            '<thead><tr><th>a</th></tr></thead>',
            '<thead><tr><th>a</th></tr></thead>'
        );
    }

    /**
     * @test
     */
    public function testTbodyOnlyNotRemoved(): void
    {
        $this->assertResult(
            '<tbody><tr><th>a</th></tr></tbody>',
            '<tbody><tr><th>a</th></tr></tbody>'
        );
    }

    /**
     * @test
     */
    public function testTrOverflowAndClose(): void
    {
        $this->assertResult(
            '<tr><td>a</td></tr><tr><td>b</td></tr><tbody><tr><td>c</td></tr></tbody><tr><td>d</td></tr>',
            '<tbody><tr><td>a</td></tr><tr><td>b</td></tr></tbody><tbody><tr><td>c</td></tr></tbody><tbody><tr><td>d</td></tr></tbody>'
        );
    }

    /**
     * @test
     */
    public function testDuplicateProcessing(): void
    {
        $this->assertResult(
            '<caption>1</caption><caption /><tbody /><tbody /><tfoot>1</tfoot><tfoot />',
            '<caption>1</caption><tfoot>1</tfoot><tbody /><tbody /><tbody />'
        );
    }

    /**
     * @test
     */
    public function testRemoveText(): void
    {
        $this->assertResult('foo', false);
    }

    /**
     * @test
     */
    public function testStickyWhitespaceOnTr(): void
    {
        $this->config->set('Output.Newline', "\n");
        $this->assertResult("\n   <tr />\n  <tr />\n ");
    }

    /**
     * @test
     */
    public function testStickyWhitespaceOnTSection(): void
    {
        $this->config->set('Output.Newline', "\n");
        $this->assertResult(
            "\n\t<tbody />\n\t\t<tfoot />\n\t\t\t",
            "\n\t<tfoot />\n\t\t\t<tbody />\n\t\t"
        );
    }
}
