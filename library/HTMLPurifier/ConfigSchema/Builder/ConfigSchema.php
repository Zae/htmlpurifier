<?php

declare(strict_types=1);

use HTMLPurifier\ConfigSchema;

/**
 * Converts HTMLPurifier_ConfigSchema_Interchange to our runtime
 * representation used to perform checks on user configuration.
 */
class HTMLPurifier_ConfigSchema_Builder_ConfigSchema
{
    /**
     * @param HTMLPurifier_ConfigSchema_Interchange $interchange
     *
     * @return ConfigSchema
     */
    public function build(HTMLPurifier_ConfigSchema_Interchange $interchange): ConfigSchema
    {
        $schema = new ConfigSchema();

        foreach ($interchange->directives as $d) {
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
