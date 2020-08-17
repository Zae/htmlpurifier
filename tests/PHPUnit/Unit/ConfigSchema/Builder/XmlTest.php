<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit\ConfigSchema\Builder;

use HTMLPurifier\ConfigSchema\Builder\Xml;
use HTMLPurifier\ConfigSchema\Interchange;
use HTMLPurifier\ConfigSchema\Interchange\Directive;
use HTMLPurifier\ConfigSchema\Interchange\Id;
use HTMLPurifier\Tests\Unit\TestCase;

/**
 * Class XmlTest
 *
 * @package HTMLPurifier\Tests\Unit\ConfigSchema\Builder
 */
class XmlTest extends TestCase
{
    /** @var Interchange $interchange */
    private $interchange;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        $this->interchange = new Interchange();
    }

    private function build(Interchange $interchange): string
    {
        $tmp = tempnam(sys_get_temp_dir(), __CLASS__);
        $xml = new Xml();
        $xml->openUri($tmp);

        $xml->build($this->interchange);

        $xml->flush();

        return file_get_contents($tmp);
    }

    /**
     * @test
     */
    public function test(): void
    {
        $xml = $this->build($this->interchange);

        static::assertXmlStringEqualsXmlString(
            '<?xml version="1.0" encoding="UTF-8"?><configdoc><title></title></configdoc>',
            $xml
        );
    }

    /**
     * @test
     */
    public function testWithDirectives(): void
    {
        $v = new Directive();
        $v->id = new Id('Namespace.Directive');

        $this->interchange->addDirective($v);

        $xml = $this->build($this->interchange);

        static::assertXmlStringEqualsXmlString(
            '<?xml version="1.0" encoding="UTF-8"?>
<configdoc>
 <title></title>
 <namespace id="Namespace">
  <name>Namespace</name>
  <directive id="Namespace.Directive">
   <name>Directive</name>
   <aliases/>
   <constraints>
    <type></type>
    <default>NULL</default>
   </constraints>
   <description>
    <div xmlns="http://www.w3.org/1999/xhtml"></div>
   </description>
  </directive>
 </namespace>
</configdoc>',
            $xml
        );
    }
}
