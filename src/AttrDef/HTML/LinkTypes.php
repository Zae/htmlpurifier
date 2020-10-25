<?php

declare(strict_types=1);

namespace HTMLPurifier\AttrDef\HTML;

use HTMLPurifier\AttrDef;
use HTMLPurifier\Config;
use HTMLPurifier\Context;
use HTMLPurifier\Exception;

use function is_null;

/**
 * Validates a rel/rev link attribute against a directive of allowed values
 *
 * @note We cannot use Enum because link types allow multiple
 *       values.
 * @note Assumes link types are ASCII text
 */
class LinkTypes extends AttrDef
{
    /**
     * Name config attribute to pull.
     *
     * @var string
     */
    protected $name;

    /**
     * @param string $name
     *
     * @throws Exception
     */
    public function __construct(string $name)
    {
        $configLookup = [
            'rel' => 'AllowedRel',
            'rev' => 'AllowedRev'
        ];

        if (!isset($configLookup[$name])) {
            throw new Exception('Unrecognized attribute name for link relationship');
        }

        $this->name = $configLookup[$name];
    }

    /**
     * @param string       $string
     * @param Config|null  $config
     * @param Context|null $context
     *
     * @return bool|string
     * @throws Exception
     */
    public function validate(string $string, ?Config $config, ?Context $context)
    {
        if (is_null($config)) {
            return false;
        }

        $allowed = $config->get('Attr.' . $this->name);
        if (empty($allowed)) {
            return false;
        }

        $string = $this->parseCDATA($string);
        $parts = explode(' ', $string);

        // lookup to prevent duplicates
        $ret_lookup = [];
        foreach ($parts as $part) {
            $part = strtolower(trim($part));
            if (!isset($allowed[$part])) {
                continue;
            }
            $ret_lookup[$part] = true;
        }

        if (empty($ret_lookup)) {
            return false;
        }

        return implode(' ', array_keys($ret_lookup));
    }
}
