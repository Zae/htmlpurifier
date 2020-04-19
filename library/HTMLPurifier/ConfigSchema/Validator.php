<?php

declare(strict_types=1);

use HTMLPurifier\Exception;
use HTMLPurifier\VarParserException;
use HTMLPurifier\VarParser;

/**
 * Performs validations on HTMLPurifier_ConfigSchema_Interchange
 *
 * @note If you see '// handled by InterchangeBuilder', that means a
 *       design decision in that class would prevent this validation from
 *       ever being necessary. We have them anyway, however, for
 *       redundancy.
 */
class HTMLPurifier_ConfigSchema_Validator
{
    /**
     * @type HTMLPurifier_ConfigSchema_Interchange
     */
    protected $interchange;

    /**
     * @type array
     */
    protected $aliases;

    /**
     * Context-stack to provide easy to read error messages.
     *
     * @type array
     */
    protected $context = [];

    /**
     * to test default's type.
     *
     * @type VarParser
     */
    protected $parser;

    public function __construct()
    {
        $this->parser = new VarParser();
    }

    /**
     * Validates a fully-formed interchange object.
     *
     * @param HTMLPurifier_ConfigSchema_Interchange $interchange
     *
     * @return bool
     * @throws HTMLPurifier_ConfigSchema_Exception
     */
    public function validate(HTMLPurifier_ConfigSchema_Interchange $interchange): bool
    {
        $this->interchange = $interchange;
        $this->aliases = [];

        // PHP is a bit lax with integer <=> string conversions in
        // arrays, so we don't use the identical !== comparison
        foreach ($interchange->directives as $i => $directive) {
            $id = $directive->id->toString();
            if ($i !== $id) {
                $this->error(false, "Integrity violation: key '$i' does not match internal id '$id'");
            }

            $this->validateDirective($directive);
        }

        return true;
    }

    /**
     * Validates a HTMLPurifier_ConfigSchema_Interchange_Id object.
     *
     * @param HTMLPurifier_ConfigSchema_Interchange_Id $id
     *
     * @throws HTMLPurifier_ConfigSchema_Exception
     */
    public function validateId(HTMLPurifier_ConfigSchema_Interchange_Id $id): void
    {
        $id_string = $id->toString();
        $this->context[] = "id '$id_string'";

        if (!$id instanceof HTMLPurifier_ConfigSchema_Interchange_Id) {
            // handled by InterchangeBuilder
            $this->error(false, 'is not an instance of HTMLPurifier_ConfigSchema_Interchange_Id');
        }

        // keys are now unconstrained (we might want to narrow down to A-Za-z0-9.)
        // we probably should check that it has at least one namespace
        $this->with($id, 'key')
             ->assertNotEmpty()
             ->assertIsString(); // implicit assertIsString handled by InterchangeBuilder

        array_pop($this->context);
    }

    /**
     * Validates a HTMLPurifier_ConfigSchema_Interchange_Directive object.
     *
     * @param HTMLPurifier_ConfigSchema_Interchange_Directive $d
     *
     * @throws HTMLPurifier_ConfigSchema_Exception
     * @throws Exception
     */
    public function validateDirective(HTMLPurifier_ConfigSchema_Interchange_Directive $d): void
    {
        $id = $d->id->toString();
        $this->context[] = "directive '$id'";
        $this->validateId($d->id);

        $this->with($d, 'description')
             ->assertNotEmpty();

        // BEGIN - handled by InterchangeBuilder
        $this->with($d, 'type')
             ->assertNotEmpty();
        $this->with($d, 'typeAllowsNull')
             ->assertIsBool();
        try {
            // This also tests validity of $d->type
            $this->parser->parse($d->default, $d->type, $d->typeAllowsNull);
        } catch (VarParserException $e) {
            $this->error('default', 'had error: ' . $e->getMessage());
        }
        // END - handled by InterchangeBuilder

        if (!is_null($d->allowed) || !empty($d->valueAliases)) {
            // allowed and valueAliases require that we be dealing with
            // strings, so check for that early.
            $d_int = VarParser::$types[$d->type];
            if (!isset(VarParser::$stringTypes[$d_int])) {
                $this->error('type', 'must be a string type when used with allowed or value aliases');
            }
        }

        $this->validateDirectiveAllowed($d);
        $this->validateDirectiveValueAliases($d);
        $this->validateDirectiveAliases($d);

        array_pop($this->context);
    }

    /**
     * Extra validation if $allowed member variable of
     * HTMLPurifier_ConfigSchema_Interchange_Directive is defined.
     *
     * @param HTMLPurifier_ConfigSchema_Interchange_Directive $d
     *
     * @throws HTMLPurifier_ConfigSchema_Exception
     */
    public function validateDirectiveAllowed(HTMLPurifier_ConfigSchema_Interchange_Directive $d): void
    {
        if (is_null($d->allowed)) {
            return;
        }
        $this->with($d, 'allowed')
             ->assertNotEmpty()
             ->assertIsLookup(); // handled by InterchangeBuilder

        if (is_string($d->default) && !isset($d->allowed[$d->default])) {
            $this->error('default', 'must be an allowed value');
        }

        $this->context[] = 'allowed';
        foreach ($d->allowed as $val => $x) {
            if (!is_string($val)) {
                $this->error("value $val", 'must be a string');
            }
        }

        array_pop($this->context);
    }

    /**
     * Extra validation if $valueAliases member variable of
     * HTMLPurifier_ConfigSchema_Interchange_Directive is defined.
     *
     * @param HTMLPurifier_ConfigSchema_Interchange_Directive $d
     *
     * @throws HTMLPurifier_ConfigSchema_Exception
     */
    public function validateDirectiveValueAliases(HTMLPurifier_ConfigSchema_Interchange_Directive $d): void
    {
        if (is_null($d->valueAliases)) {
            return;
        }

        $this->with($d, 'valueAliases')
             ->assertIsArray(); // handled by InterchangeBuilder

        $this->context[] = 'valueAliases';
        foreach ($d->valueAliases as $alias => $real) {
            if (!is_string($alias)) {
                $this->error("alias $alias", 'must be a string');
            }
            if (!is_string($real)) {
                $this->error("alias target $real from alias '$alias'", 'must be a string');
            }
            if ($alias === $real) {
                $this->error("alias '$alias'", 'must not be an alias to itself');
            }
        }

        if (!is_null($d->allowed)) {
            foreach ($d->valueAliases as $alias => $real) {
                if (isset($d->allowed[$alias])) {
                    $this->error("alias '$alias'", 'must not be an allowed value');
                } elseif (!isset($d->allowed[$real])) {
                    $this->error("alias '$alias'", 'must be an alias to an allowed value');
                }
            }
        }

        array_pop($this->context);
    }

    /**
     * Extra validation if $aliases member variable of
     * HTMLPurifier_ConfigSchema_Interchange_Directive is defined.
     *
     * @param HTMLPurifier_ConfigSchema_Interchange_Directive $d
     *
     * @throws HTMLPurifier_ConfigSchema_Exception
     */
    public function validateDirectiveAliases(HTMLPurifier_ConfigSchema_Interchange_Directive $d): void
    {
        $this->with($d, 'aliases')
             ->assertIsArray(); // handled by InterchangeBuilder

        $this->context[] = 'aliases';
        foreach ($d->aliases as $alias) {
            $this->validateId($alias);
            $s = $alias->toString();
            if (isset($this->interchange->directives[$s])) {
                $this->error("alias '$s'", 'collides with another directive');
            }

            if (isset($this->aliases[$s])) {
                $other_directive = $this->aliases[$s];
                $this->error("alias '$s'", "collides with alias for directive '$other_directive'");
            }

            $this->aliases[$s] = $d->id->toString();
        }

        array_pop($this->context);
    }

    // protected helper functions

    /**
     * Convenience function for generating HTMLPurifier_ConfigSchema_ValidatorAtom
     * for validating simple member variables of objects.
     *
     * @param object $obj
     * @param string $member
     *
     * @return HTMLPurifier_ConfigSchema_ValidatorAtom
     */
    protected function with($obj, string $member): HTMLPurifier_ConfigSchema_ValidatorAtom
    {
        return new HTMLPurifier_ConfigSchema_ValidatorAtom($this->getFormattedContext(), $obj, $member);
    }

    /**
     * Emits an error, providing helpful context.
     *
     * @param string $target
     * @param string $msg
     *
     * @throws HTMLPurifier_ConfigSchema_Exception
     */
    protected function error($target, $msg): void
    {
        if ($target !== false) {
            $prefix = ucfirst($target) . ' in ' . $this->getFormattedContext();
        } else {
            $prefix = ucfirst($this->getFormattedContext());
        }

        throw new HTMLPurifier_ConfigSchema_Exception(trim($prefix . ' ' . $msg));
    }

    /**
     * Returns a formatted context string.
     *
     * @return string
     */
    protected function getFormattedContext(): string
    {
        return implode(' in ', array_reverse($this->context));
    }
}
