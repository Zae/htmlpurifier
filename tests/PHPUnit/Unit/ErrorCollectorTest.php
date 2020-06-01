<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit;

use HTMLPurifier\ErrorCollector;
use HTMLPurifier\Generator;
use HTMLPurifier\Language;
use HTMLPurifier\Token\Start;
use Mockery;

/**
 * Class ErrorCollectorTest
 *
 * @package HTMLPurifier\Tests\Unit
 */
class ErrorCollectorTest extends TestCase
{
    protected $language, $generator, $line;
    protected $collector;

    protected function setUp(): void
    {
        parent::setUp();

        $this->language = Mockery::mock(Language::class);

//        $this->language->expects()
//            ->getErrorName(E_ERROR)
//            ->once()
//            ->andReturn('Error');

//        $this->language->expects()
//            ->getErrorName(E_WARNING)
//            ->once()
//            ->andReturn('Warning');

//        $this->language->expects()
//            ->getErrorName(E_NOTICE)
//            ->once()
//            ->andReturn('Notice');

        // this might prove to be troublesome if we need to set config
        $this->generator = new Generator($this->config, $this->context);
        $this->line = false;
        $this->context->register('Locale', $this->language);
        $this->context->register('CurrentLine', $this->line);
        $this->context->register('Generator', $this->generator);
        $this->collector = new ErrorCollector($this->context);
    }

    /**
     * @test
     */
    public function test(): void
    {
        $language = $this->language;
        $language->expects()
            ->getMessage('message-1')
            ->once()
            ->andReturn('Message 1');

        $language->expects()
            ->formatMessage('message-2', [1 => 'param'])
            ->once()
            ->andReturn('Message 2');

//        $language->expects()
//            ->formatMessage('ErrorCollector: At line', ['line' => 23])
//            ->once()
//            ->andReturn(' at line 23');

//        $language->expects()
//            ->formatMessage('ErrorCollector: At line', ['line' => 3])
//            ->once()
//            ->andReturn(' at line 3');

        $this->line = 23;
        $this->collector->send(E_ERROR, 'message-1');

        $this->line = 3;
        $this->collector->send(E_WARNING, 'message-2', 'param');

        $result = [
            0 => [23, E_ERROR, 'Message 1', []],
            1 => [3, E_WARNING, 'Message 2', []]
        ];

        static::assertEquals($result, $this->collector->getRaw());

        /*
        $formatted_result =
            '<ul><li><strong>Warning</strong>: Message 2 at line 3</li>'.
            '<li><strong>Error</strong>: Message 1 at line 23</li></ul>';

        $this->assertIdentical($this->collector->getHTMLFormatted($this->config), $formatted_result);
        */
    }

    /**
     * @test
     */
    public function testNoErrors(): void
    {
        $this->language->expects()
            ->getMessage('ErrorCollector: No errors')
            ->andReturn('No errors');

        $formatted_result = '<p>No errors</p>';
        static::assertEquals(
            $formatted_result,
            $this->collector->getHTMLFormatted($this->config)
        );
    }

    /**
     * @test
     */
    public function testNoLineNumbers(): void
    {
        $this->language->expects()
            ->getMessage('message-1')
            ->andReturn('Message 1');

        $this->language->expects()
            ->getMessage('message-2')
            ->andReturn('Message 2');

        $this->collector->send(E_ERROR, 'message-1');
        $this->collector->send(E_ERROR, 'message-2');

        $result = [
            0 => [false, E_ERROR, 'Message 1', []],
            1 => [false, E_ERROR, 'Message 2', []]
        ];
        static::assertEquals($result, $this->collector->getRaw());

        /*
        $formatted_result =
            '<ul><li><strong>Error</strong>: Message 1</li>'.
            '<li><strong>Error</strong>: Message 2</li></ul>';
        $this->assertIdentical($this->collector->getHTMLFormatted($this->config), $formatted_result);
        */
    }

    /**
     * @test
     */
    public function testContextSubstitutions(): void
    {
        $current_token = false;
        $this->context->register('CurrentToken', $current_token);

        // 0
        $current_token = new Start('a', ['href' => 'http://example.com'], 32);

        $this->language->expects()
            ->formatMessage('message-data-token', ['CurrentToken' => $current_token])
            ->once()
            ->andReturn('Token message');

        $this->collector->send(E_NOTICE, 'message-data-token');

        $current_attr  = 'href';
        $this->language->expects()
            ->formatMessage('message-attr', ['CurrentToken' => $current_token])
            ->twice()
            ->andReturn('$CurrentAttr.Name => $CurrentAttr.Value');

        // 1
        $this->collector->send(E_NOTICE, 'message-attr'); // test when context isn't available

        // 2
        $this->context->register('CurrentAttr', $current_attr);
        $this->collector->send(E_NOTICE, 'message-attr');

        $result = [
            0 => [32, E_NOTICE, 'Token message', []],
            1 => [32, E_NOTICE, '$CurrentAttr.Name => $CurrentAttr.Value', []],
            2 => [32, E_NOTICE, 'href => http://example.com', []]
        ];

        static::assertEquals($result, $this->collector->getRaw());
    }

    /*
    public function testNestedErrors()
    {
        $this->language->returns('getMessage', 'Message 1',   array('message-1'));
        $this->language->returns('getMessage', 'Message 2',   array('message-2'));
        $this->language->returns('formatMessage', 'End Message', array('end-message', array(1 => 'param')));
        $this->language->returns('formatMessage', ' at line 4', array('ErrorCollector: At line', array('line' => 4)));

        $this->line = 4;
        $this->collector->start();
        $this->collector->send(E_WARNING, 'message-1');
        $this->collector->send(E_NOTICE,  'message-2');
        $this->collector->end(E_NOTICE, 'end-message', 'param');

        $expect = array(
            0 => array(4, E_NOTICE, 'End Message', array(
                0 => array(4, E_WARNING, 'Message 1', array()),
                1 => array(4, E_NOTICE,  'Message 2', array()),
            )),
        );
        $result = $this->collector->getRaw();
        $this->assertIdentical($result, $expect);

        $formatted_expect =
            '<ul><li><strong>Notice</strong>: End Message at line 4<ul>'.
                '<li><strong>Warning</strong>: Message 1 at line 4</li>'.
                '<li><strong>Notice</strong>: Message 2 at line 4</li></ul>'.
            '</li></ul>';
        $formatted_result = $this->collector->getHTMLFormatted($this->config);
        $this->assertIdentical($formatted_result, $formatted_expect);

    }
    */
}
