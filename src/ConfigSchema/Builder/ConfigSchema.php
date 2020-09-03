<?php

declare(strict_types=1);

namespace HTMLPurifier\ConfigSchema\Builder;

use HTMLPurifier\ConfigSchema\Interchange;
use HTMLPurifier\Exception;

/**
 * Converts HTMLPurifier_ConfigSchema_Interchange to our runtime
 * representation used to perform checks on user configuration.
 */
class ConfigSchema
{
    /**
     * @param Interchange $interchange
     *
     * @return \HTMLPurifier\ConfigSchema
     * @throws \Exception
     */
    public function build(Interchange $interchange): \HTMLPurifier\ConfigSchema
    {
        $schema = new \HTMLPurifier\ConfigSchema();

        foreach ($interchange->directives as $d) {
            if (!$d->id instanceof Interchange\Id) {
                throw new Exception('Directive id is wrong type');
            }

            $schema->add(
                $d->id->key,
                $d->default,
                $d->type,
                $d->typeAllowsNull
            );

            if ($d->allowed !== null) {
                $schema->addAllowedValues(
                    $d->id->key,
                    $d->allowed
                );
            }

            foreach ($d->aliases as $alias) {
                $schema->addAlias(
                    $alias->key,
                    $d->id->key
                );
            }

            if ($d->valueAliases !== null) {
                $schema->addValueAliases(
                    $d->id->key,
                    $d->valueAliases
                );
            }
        }

        $schema->postProcess();

        return $schema;
    }
}
