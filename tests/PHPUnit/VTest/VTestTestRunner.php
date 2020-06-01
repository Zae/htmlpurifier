<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\VTest;

use HTMLPurifier\StringHash;
use HTMLPurifier\StringHashParser;
use HTMLPurifier\Tests\Unit\TestCase;
use HTMLPurifier\ConfigSchema\Interchange;
use HTMLPurifier\ConfigSchema\InterchangeBuilder;
use HTMLPurifier\ConfigSchema\Validator;

/**
 * Class VTestTestRunner
 */
class VTestTestRunner extends TestCase
{
    protected $_path;
    protected $_parser;
    protected $_builder;
    private $validator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->_parser  = new StringHashParser();
        $this->_builder = new InterchangeBuilder();
        $this->validator = new Validator();
    }

    /**
     * @return array[]
     */
    public function getFiles(): array
    {
        return array_map(static function ($path) {
            return [$path];
        }, glob(__DIR__ . '/*.vtest'));
    }

    /**
     * @test
     * @dataProvider getFiles
     */
    public function testValidator(string $file): void
    {
        $hashes = $this->_parser->parseMultiFile($file);
        $interchange = new Interchange();
        $error = null;

        foreach ($hashes as $hash) {
            if (!isset($hash['ID'])) {
                if (isset($hash['ERROR'])) {
                    $this->expectException(\Exception::class);
                    $this->expectExceptionMessage($hash['ERROR']);
                }

                continue;
            }

            $this->_builder->build($interchange, new StringHash($hash));
        }

        $this->validator->validate($interchange);
        static::assertReached();
    }
}
