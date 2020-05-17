<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit;

use HTMLPurifier\Config;
use HTMLPurifier\Context;
use HTMLPurifier\Exception;
use HTMLPurifier\Generator;
use HTMLPurifier\Language;
use HTMLPurifier\LanguageFactory;
use HTMLPurifier\Token\Start;
use HTMLPurifier\Token\Text;

/**
 * Class LanguageTest
 *
 * @package HTMLPurifier\Tests\Unit
 */
class LanguageTest extends TestCase
{
    /**
     * @return Language
     * @throws Exception
     */
    private function generateEnLanguage(): Language
    {
        $factory = LanguageFactory::instance();
        $config = Config::create(['Core.Language' => 'en']);
        $context = new Context();
        return $factory->create($config, $context);
    }

    /**
     * @test
     */
    public function test_getMessage(): void
    {
        $config = Config::createDefault();
        $context = new Context();
        $lang = new Language($config, $context);
        $lang->loaded = true;
        $lang->messages['HTMLPurifier'] = 'HTML Purifier';

        static::assertEquals('HTML Purifier', $lang->getMessage('HTMLPurifier'));
        static::assertEquals(
            '[LanguageTest: Totally non-existent key]',
            $lang->getMessage('LanguageTest: Totally non-existent key')
        );
    }

    /**
     * @test
     */
    public function test_formatMessage(): void
    {
        $config = Config::createDefault();
        $context = new Context();
        $lang = new Language($config, $context);
        $lang->loaded = true;
        $lang->messages['LanguageTest: Error'] = 'Error is $1 on line $2';

        static::assertEquals(
            'Error is fatal on line 32',
            $lang->formatMessage('LanguageTest: Error', [1 => 'fatal', 32])
        );
    }

    /**
     * @test
     */
    public function test_formatMessage_tokenParameter(): void
    {
        $config = Config::createDefault();
        $context = new Context();
        $generator = new Generator($config, $context); // replace with mock if this gets icky
        $context->register('Generator', $generator);
        $lang = new Language($config, $context);
        $lang->loaded = true;
        $lang->messages['LanguageTest: Element info'] = 'Element Token: $1.Name, $1.Serialized, $1.Compact, $1.Line';
        $lang->messages['LanguageTest: Data info']    = 'Data Token: $1.Data, $1.Serialized, $1.Compact, $1.Line';

        static::assertEquals(
            'Element Token: a, <a href="http://example.com">, <a>, 18',
            $lang->formatMessage(
                'LanguageTest: Element info',
                [1 => new Start('a', ['href'=>'http://example.com'], 18)]
            )
        );

        static::assertEquals(
            'Data Token: data>, data&gt;, data&gt;, 23',
            $lang->formatMessage(
                'LanguageTest: Data info',
                [1 => new Text('data>', 23)]
            )
        );
    }

    /**
     * @test
     * @throws Exception
     */
    public function test_listify(): void
    {
        $lang = $this->generateEnLanguage();
        static::assertEquals('Item', $lang->listify(['Item']));
        static::assertEquals('Item and Item2', $lang->listify(['Item', 'Item2']));
        static::assertEquals('Item, Item2 and Item3', $lang->listify(['Item', 'Item2', 'Item3']));
    }

    /**
     * @test
     * @throws Exception
     */
    public function test_formatMessage_arrayParameter(): void
    {
        $lang = $this->generateEnLanguage();

        $array = ['Item1', 'Item2', 'Item3'];
        static::assertEquals(
            'Item1, Item2 and Item3',
            $lang->formatMessage('LanguageTest: List', [1 => $array])
        );

        $array = ['Key1' => 'Value1', 'Key2' => 'Value2'];
        static::assertEquals(
            'Key1 and Key2; Value1 and Value2',
            $lang->formatMessage('LanguageTest: Hash', [1 => $array])
        );
    }
}
