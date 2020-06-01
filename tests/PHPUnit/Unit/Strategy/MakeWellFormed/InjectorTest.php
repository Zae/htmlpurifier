<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit\Strategy\MakeWellFormed;

use HTMLPurifier\Tests\Unit\Strategy\TestCase;
use HTMLPurifier\Injector;
use HTMLPurifier\Strategy\MakeWellFormed;
use HTMLPurifier\Token\End;
use HTMLPurifier\Token\Start;
use Mockery;

/**
 * Class InjectorTest
 *
 * @package HTMLPurifier\Tests\Unit\Strategy\MakeWellFormed
 */
class InjectorTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->obj = new MakeWellFormed();
        $this->config->set('AutoFormat.AutoParagraph', true);
        $this->config->set('AutoFormat.Linkify', true);
        $this->config->set('AutoFormat.RemoveEmpty', true);
    }

    /**
     * @test
     * @throws \HTMLPurifier\Exception
     */
    public function testEndHandler(): void
    {
        $this->markAsRisky();
        $mock = Mockery::mock(Injector::class);

        $b = new End('b');
        $b->skip = [0 => true];
        $b->start = new Start('b');
        $b->start->skip = [0 => true, 1 => true];

        $mock->expects()
             ->handleEnd(Mockery::any())
             ->twice();

//        $mock->expects()
//             ->handleEnd($b)
//             ->once();

        $mock->expects()
             ->handleElement(Mockery::any())
             ->twice();

        $mock->expects()
             ->handleText(Mockery::any())
             ->once();

        $i = new End('i');
        $i->start = new Start('i');
        $i->skip = [0 => true];
        $i->start->skip = [0 => true, 1 => true];

//        $mock->expects()
//            ->handleEnd($i)
//            ->once()
//            ->andReturn();

        $mock->expects()
            ->getRewindOffset()
            ->times(10)
            ->andReturns(false);

        $mock->expects()
            ->prepare($this->config, $this->context)
            ->once();

        $this->config->set('AutoFormat.AutoParagraph', false);
        $this->config->set('AutoFormat.Linkify',       false);
        $this->config->set('AutoFormat.Custom', [$mock]);

        $this->assertResult('<i><b>asdf</b>', '<i><b>asdf</b></i>');
    }

    /**
     * @test
     * @throws \HTMLPurifier\Exception
     */
    public function testErrorRequiredElementNotAllowed(): void
    {
        $this->markAsRisky();
        $this->config->set('HTML.Allowed', '');

        $this->expectError();
        $this->expectErrorMessage('Cannot enable AutoParagraph injector because p is not allowed');
//        $this->expectErrorMessage('Cannot enable Linkify injector because a is not allowed');

        $this->assertResult('Foobar');
    }

    /**
     * @test
     * @throws \HTMLPurifier\Exception
     */
    public function testErrorRequiredAttributeNotAllowed(): void
    {
        $this->config->set('HTML.Allowed', 'a,p');

        $this->expectError();
        $this->expectErrorMessage('Cannot enable Linkify injector because a.href is not allowed');

        $this->assertResult('<p>http://example.com</p>');
    }

    /**
     * @test
     * @throws \HTMLPurifier\Exception
     */
    public function testOnlyAutoParagraph(): void
    {
        $this->assertResult(
            'Foobar',
            '<p>Foobar</p>'
        );
    }

    /**
     * @test
     */
    public function testParagraphWrappingOnlyLink(): void
    {
        $this->assertResult(
            'http://example.com',
            '<p><a href="http://example.com">http://example.com</a></p>'
        );
    }

    /**
     * @test
     */
    public function testParagraphWrappingNodeContainingLink(): void
    {
        $this->assertResult(
            '<b>http://example.com</b>',
            '<p><b><a href="http://example.com">http://example.com</a></b></p>'
        );
    }

    /**
     * @test
     */
    public function testParagraphWrappingPoorlyFormedNodeContainingLink(): void
    {
        $this->assertResult(
            '<b>http://example.com',
            '<p><b><a href="http://example.com">http://example.com</a></b></p>'
        );
    }

    /**
     * @test
     */
    public function testTwoParagraphsContainingOnlyOneLink(): void
    {
        $this->assertResult(
            "http://example.com\n\nhttp://dev.example.com",
            '<p><a href="http://example.com">http://example.com</a></p>

<p><a href="http://dev.example.com">http://dev.example.com</a></p>'
        );
    }

    /**
     * @test
     */
    public function testParagraphNextToDivWithLinks(): void
    {
        $this->assertResult(
            'http://example.com <div>http://example.com</div>',
            '<p><a href="http://example.com">http://example.com</a> </p>

<div><a href="http://example.com">http://example.com</a></div>'
        );
    }

    /**
     * @test
     */
    public function testRealisticLinkInSentence(): void
    {
        $this->assertResult(
            'This URL http://example.com is what you need',
            '<p>This URL <a href="http://example.com">http://example.com</a> is what you need</p>'
        );
    }

    /**
     * @test
     */
    public function testParagraphAfterLinkifiedURL(): void
    {
        $this->assertResult(
            "http://google.com

<b>b</b>",
            "<p><a href=\"http://google.com\">http://google.com</a></p>

<p><b>b</b></p>"
        );
    }

    /**
     * @test
     */
    public function testEmptyAndParagraph(): void
    {
        // This is a fairly degenerate case, but it demonstrates that
        // the two don't error out together, at least.
        // Change this behavior!
        $this->assertResult(
            "<p>asdf

asdf<b></b></p>

<p></p><i></i>",
            "<p>asdf</p>

<p>asdf</p>

"
        );
    }

    /**
     * @test
     */
    public function testRewindAndParagraph(): void
    {
        $this->assertResult(
            "bar

<p><i></i>

</p>

foo",
            "<p>bar</p>



<p>foo</p>"
        );
    }
}
