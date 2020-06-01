<?php

declare(strict_types=1);

namespace HTMLPurifier\ConfigSchema\Builder;

use HTMLPurifier\ConfigSchema\Interchange;

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
     */
    public function build(Interchange $interchange): \HTMLPurifier\ConfigSchema
    {
        $schema = new \HTMLPurifier\ConfigSchema();

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
