<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\HTMLT;

use HTMLPurifier\StringHashParser;
use HTMLPurifier\Tests\Unit\TestCase;

/**
 * Class HtmltTestRunner
 */
class HtmltTestRunner extends TestCase
{
    /**
     * @return array[]
     */
    public function getFiles(): array
    {
        return array_map(static function ($path) {
            return [$path];
        }, glob(__DIR__ . '/*.htmlt'));
    }

    /**
     * @test
     * @dataProvider getFiles
     *
     * @param string $file
     *
     * @throws \HTMLPurifier\Exception
     */
    public function testHtmlt(string $file): void
    {
        $parser = new StringHashParser();
        /** @var array $hash */
        $hash = $parser->parseFile($file); // assume parser normalizes to "\n"

        if (isset($hash['SKIPIF']) && eval($hash['SKIPIF'])) {
            return;
        }

        $this->config->set('Output.Newline', "\n");
        if (isset($hash['INI'])) {
            // there should be a more efficient way than writing another
            // ini file every time... probably means building a parser for
            // ini (check out the yaml implementation we saw somewhere else)
            $ini_file = tempnam(sys_get_temp_dir(), 'htmlt');
            file_put_contents($ini_file, $hash['INI']);
            $this->config->loadIni($ini_file);
        }

        $expect = $hash['EXPECT'] ?? $hash['HTML'];
        if (isset($hash['ERROR'])) {
            $this->expectError();
            $this->expectErrorMessage($hash['ERROR']);
        }

        $this->assertPurification(rtrim($hash['HTML']), rtrim($expect));

        if (isset($hash['INI'])) {
            @unlink($ini_file);
        }
    }
}
