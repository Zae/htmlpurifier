<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit\Strategy;

use HTMLPurifier\Exception;
use HTMLPurifier\Strategy\FixNesting;

/**
 * Class FixNestingTest
 *
 * @package HTMLPurifier\Tests\Unit\Strategy
 */
class FixNestingTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->obj = new FixNesting();
    }

    /**
     * @test
     */
    public function testPreserveInlineInRoot(): void
    {
        $this->assertResult('<b>Bold text</b>');
    }

    /**
     * @test
     */
    public function testPreserveInlineAndBlockInRoot(): void
    {
        $this->assertResult('<a href="about:blank">Blank</a><div>Block</div>');
    }

    /**
     * @test
     */
    public function testRemoveBlockInInline(): void
    {
        $this->assertResult(
            '<b><div>Illegal div.</div></b>',
            '<b>Illegal div.</b>'
        );
    }

    /**
     * @test
     */
    public function testRemoveNodeWithMissingRequiredElements(): void
    {
        $this->assertResult('<ul></ul>', '');
    }

    /**
     * @test
     */
    public function testListHandleIllegalPCDATA(): void
    {
        $this->assertResult(
            '<ul>Illegal text<li>Legal item</li></ul>',
            '<ul><li>Illegal text</li><li>Legal item</li></ul>'
        );
    }

    /**
     * @test
     */
    public function testRemoveIllegalPCDATA(): void
    {
        $this->assertResult(
            '<table><tr>Illegal text<td></td></tr></table>',
            '<table><tr><td></td></tr></table>'
        );
    }

    /**
     * @test
     */
    public function testCustomTableDefinition(): void
    {
        $this->assertResult('<table><tr><td>Cell 1</td></tr></table>');
    }

    /**
     * @test
     */
    public function testRemoveEmptyTable(): void
    {
        $this->assertResult('<table></table>', '');
    }

    /**
     * @test
     */
    public function testChameleonRemoveBlockInNodeInInline(): void
    {
        $this->assertResult(
            '<span><ins><div>Not allowed!</div></ins></span>',
            '<span><ins>Not allowed!</ins></span>'
        );
    }

    /**
     * @test
     */
    public function testChameleonRemoveBlockInBlockNodeWithInlineContent(): void
    {
        $this->assertResult(
            '<h1><ins><div>Not allowed!</div></ins></h1>',
            '<h1><ins>Not allowed!</ins></h1>'
        );
    }

    /**
     * @test
     */
    public function testNestedChameleonRemoveBlockInNodeWithInlineContent(): void
    {
        $this->assertResult(
            '<h1><ins><del><div>Not allowed!</div></del></ins></h1>',
            '<h1><ins><del>Not allowed!</del></ins></h1>'
        );
    }

    /**
     * @test
     */
    public function testNestedChameleonPreserveBlockInBlock(): void
    {
        $this->assertResult(
            '<div><ins><del><div>Allowed!</div></del></ins></div>'
        );
    }

    /**
     * @test
     */
    public function testExclusionsIntegration(): void
    {
        // test exclusions
        $this->assertResult(
            '<a><span><a>Not allowed</a></span></a>',
            '<a><span></span></a>'
        );
    }

    /**
     * @test
     */
    public function testPreserveInlineNodeInInlineRootNode(): void
    {
        $this->config->set('HTML.Parent', 'span');
        $this->assertResult('<b>Bold</b>');
    }

    /**
     * @test
     */
    public function testRemoveBlockNodeInInlineRootNode(): void
    {
        $this->config->set('HTML.Parent', 'span');
        $this->assertResult('<div>Reject</div>', 'Reject');
    }

    /**
     * @test
     */
    public function testInvalidParentError(): void
    {
        // test fallback to div
        $this->config->set('HTML.Parent', 'obviously-impossible');
        $this->config->set('Cache.DefinitionImpl', null);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Cannot use unrecognized element as parent');

        $this->assertResult('<div>Accept</div>');
    }

    /**
     * @test
     */
    public function testCascadingRemovalOfNodesMissingRequiredChildren(): void
    {
        $this->assertResult('<table><tr></tr></table>', '');
    }

    /**
     * @test
     */
    public function testCascadingRemovalSpecialCaseCannotScrollOneBack(): void
    {
        $this->assertResult('<table><tr></tr><tr></tr></table>', '');
    }

    /**
     * @test
     */
    public function testLotsOfCascadingRemovalOfNodes(): void
    {
        $this->assertResult('<table><tbody><tr></tr><tr></tr></tbody><tr></tr><tr></tr></table>', '');
    }

    /**
     * @test
     */
    public function testAdjacentRemovalOfNodeMissingRequiredChildren(): void
    {
        $this->assertResult('<table></table><table></table>', '');
    }

    /**
     * @test
     */
    public function testStrictBlockquoteInHTML401(): void
    {
        $this->config->set('HTML.Doctype', 'HTML 4.01 Strict');
        $this->assertResult('<blockquote>text</blockquote>', '<blockquote><p>text</p></blockquote>');
    }

    /**
     * @test
     */
    public function testDisabledExcludes(): void
    {
        $this->config->set('Core.DisableExcludes', true);
        $this->assertResult('<pre><font><font></font></font></pre>');
    }

    /**
     * @test
     */
    public function testDoubleKill(): void
    {
        $this->config->set('HTML.Allowed', 'ul');
        $this->expectError('Cannot allow ul/ol without allowing li');
        $this->assertResult('<ul>foo</ul>', '');
    }
}
