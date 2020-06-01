<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit;

use HTMLPurifier\ConfigSchema;
use HTMLPurifier\VarParser;

/**
 * Class ConfigSchemaTest
 *
 * @package HTMLPurifier\Tests\Unit
 */
class ConfigSchemaTest extends TestCase
{
    private $schema;

    protected function setUp(): void
    {
        $this->schema = new ConfigSchema();
    }

    /**
     * @test
     */
    public function test_define(): void
    {
        $this->schema->add('Car.Seats', 5, 'int', false);

        static::assertEquals(5, $this->schema->defaults['Car.Seats']);
        $this->assertEquals($this->schema->info['Car.Seats']->type, VarParser::C_INT);

        $this->schema->add('Car.Age', null, 'int', true);

        static::assertEquals(null, $this->schema->defaults['Car.Age']);
        static::assertEquals(VarParser::C_INT, $this->schema->info['Car.Age']->type);
    }

    /**
     * @test
     */
    public function test_defineAllowedValues(): void
    {
        $this->schema->add('QuantumNumber.Spin', 0.5, 'float', false);
        $this->schema->add('QuantumNumber.Current', 's', 'string', false);
        $this->schema->add('QuantumNumber.Difficulty', null, 'string', true);

        $this->schema->addAllowedValues( // okay, since default is null
            'QuantumNumber.Difficulty', ['easy' => true, 'medium' => true, 'hard' => true]
        );

        static::assertEquals(null, $this->schema->defaults['QuantumNumber.Difficulty']);
        static::assertEquals(VarParser::C_STRING, $this->schema->info['QuantumNumber.Difficulty']->type);
        static::assertEquals(true, $this->schema->info['QuantumNumber.Difficulty']->allow_null);
        static::assertEquals([
            'easy' => true,
            'medium' => true,
            'hard' => true
        ],
            $this->schema->info['QuantumNumber.Difficulty']->allowed
        );
    }

    /**
     * @test
     */
    public function test_defineValueAliases(): void
    {
        $this->schema->add('Abbrev.HTH', 'Happy to Help', 'string', false);
        $this->schema->addAllowedValues(
            'Abbrev.HTH', [
                'Happy to Help' => true,
                'Hope that Helps' => true,
                'HAIL THE HAND!' => true,
            ]
        );
        $this->schema->addValueAliases(
            'Abbrev.HTH', [
                'happy' => 'Happy to Help',
                'hope' => 'Hope that Helps'
            ]
        );
        $this->schema->addValueAliases( // delayed addition
            'Abbrev.HTH', [
                'hail' => 'HAIL THE HAND!'
            ]
        );

        static::assertEquals('Happy to Help', $this->schema->defaults['Abbrev.HTH']);
        static::assertEquals(VarParser::C_STRING, $this->schema->info['Abbrev.HTH']->type);
        static::assertEquals([
            'Happy to Help' => true,
            'Hope that Helps' => true,
            'HAIL THE HAND!' => true
        ],
            $this->schema->info['Abbrev.HTH']->allowed
        );
        static::assertEquals([
            'happy' => 'Happy to Help',
            'hope' => 'Hope that Helps',
            'hail' => 'HAIL THE HAND!'
        ],
            $this->schema->info['Abbrev.HTH']->aliases
        );
    }

    /**
     * @test
     */
    public function testAlias(): void
    {
        $this->schema->add('Home.Rug', 3, 'int', false);
        $this->schema->addAlias('Home.Carpet', 'Home.Rug');

        static::assertNotTrue(isset($this->schema->defaults['Home.Carpet']));
        static::assertEquals('Home.Rug', $this->schema->info['Home.Carpet']->key);
        static::assertEquals(true, $this->schema->info['Home.Carpet']->isAlias);
    }
}
