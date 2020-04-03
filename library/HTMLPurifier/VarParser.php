<?php

declare(strict_types=1);

/**
 * Parses string representations into their corresponding native PHP
 * variable type. The base implementation does a simple type-check.
 */
class HTMLPurifier_VarParser
{
    public const C_STRING = 1;
    public const ISTRING  = 2;
    public const TEXT     = 3;
    public const ITEXT    = 4;
    public const C_INT    = 5;
    public const C_FLOAT  = 6;
    public const C_BOOL   = 7;
    public const LOOKUP   = 8;
    public const ALIST    = 9;
    public const HASH     = 10;
    public const C_MIXED  = 11;

    /**
     * Lookup table of allowed types. Mainly for backwards compatibility, but
     * also convenient for transforming string type names to the integer constants.
     */
    public static $types = [
        'string' => self::C_STRING,
        'istring' => self::ISTRING,
        'text' => self::TEXT,
        'itext' => self::ITEXT,
        'int' => self::C_INT,
        'float' => self::C_FLOAT,
        'bool' => self::C_BOOL,
        'lookup' => self::LOOKUP,
        'list' => self::ALIST,
        'hash' => self::HASH,
        'mixed' => self::C_MIXED
    ];

    /**
     * Lookup table of types that are string, and can have aliases or
     * allowed value lists.
     */
    public static $stringTypes = [
        self::C_STRING => true,
        self::ISTRING => true,
        self::TEXT => true,
        self::ITEXT => true,
    ];

    /**
     * Validate a variable according to type.
     * It may return NULL as a valid type if $allow_null is true.
     *
     * @param mixed      $var        Variable to validate
     * @param int|string $type       Type of variable, see HTMLPurifier_VarParser->types
     * @param bool       $allow_null Whether or not to permit null as a value
     *
     * @return mixed Validated and type-coerced variable
     * @throws HTMLPurifier_VarParserException|HTMLPurifier_Exception
     */
    final public function parse($var, $type, bool $allow_null = false)
    {
        if (is_string($type)) {
            if (!isset(static::$types[$type])) {
                throw new HTMLPurifier_VarParserException("Invalid type '$type'");
            }

            $type = static::$types[$type];
        }

        $var = $this->parseImplementation($var, $type, $allow_null);
        if ($allow_null && $var === null) {
            return null;
        }

        // These are basic checks, to make sure nothing horribly wrong
        // happened in our implementations.
        switch ($type) {
            case (self::C_STRING):
            case (self::ISTRING):
            case (self::TEXT):
            case (self::ITEXT):
                if (!is_string($var)) {
                    break;
                }
                if ($type === self::ISTRING || $type === self::ITEXT) {
                    $var = strtolower($var);
                }

                return $var;
            case (self::C_INT):
                if (!is_int($var)) {
                    break;
                }

                return $var;
            case (self::C_FLOAT):
                if (!is_float($var)) {
                    break;
                }

                return $var;
            case (self::C_BOOL):
                if (!is_bool($var)) {
                    break;
                }

                return $var;
            case (self::LOOKUP):
            case (self::ALIST):
            case (self::HASH):
                if (!is_array($var)) {
                    break;
                }

                if ($type === self::LOOKUP) {
                    foreach ($var as $k) {
                        if ($k !== true) {
                            $this->error('Lookup table contains value other than true');
                        }
                    }
                } elseif ($type === self::ALIST) {
                    $keys = array_keys($var);
                    if (array_keys($keys) !== $keys) {
                        $this->error('Indices for list are not uniform');
                    }
                }

                return $var;
            case (self::C_MIXED):
                return $var;
            default:
                $this->errorInconsistent(get_class($this), $type);
        }

        $this->errorGeneric($var, $type);
    }

    /**
     * Actually implements the parsing. Base implementation does not
     * do anything to $var. Subclasses should overload this!
     *
     * @param mixed $var
     * @param int   $type
     * @param bool  $allow_null
     *
     * @return mixed
     */
    protected function parseImplementation($var, int $type, bool $allow_null)
    {
        return $var;
    }

    /**
     * Throws an exception.
     *
     * @param string $msg
     *
     * @throws HTMLPurifier_VarParserException
     */
    protected function error(string $msg): void
    {
        throw new HTMLPurifier_VarParserException($msg);
    }

    /**
     * Throws an inconsistency exception.
     *
     * @note This should not ever be called. It would be called if we
     *       extend the allowed values of HTMLPurifier_VarParser without
     *       updating subclasses.
     *
     * @param string $class
     * @param int    $type
     *
     * @throws HTMLPurifier_Exception
     */
    protected function errorInconsistent(string $class, int $type): void
    {
        throw new HTMLPurifier_Exception(
            "Inconsistency in $class: " . static::getTypeName($type) . ' not implemented'
        );
    }

    /**
     * Generic error for if a type didn't work.
     *
     * @param mixed $var
     * @param int   $type
     *
     * @throws HTMLPurifier_VarParserException
     */
    protected function errorGeneric($var, int $type): void
    {
        $vtype = gettype($var);
        $this->error('Expected type ' . static::getTypeName($type) . ", got $vtype");
    }

    /**
     * @param int $type
     *
     * @return string
     */
    public static function getTypeName(int $type): string
    {
        static $lookup;
        if (!$lookup) {
            // Lazy load the alternative lookup table
            $lookup = array_flip(static::$types);
        }

        if (!isset($lookup[$type])) {
            return 'unknown';
        }

        return $lookup[$type];
    }
}
