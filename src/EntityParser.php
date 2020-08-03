<?php

declare(strict_types=1);

// if want to implement error collecting here, we'll need to use some sort
// of global data (probably trigger_error) because it's impossible to pass
// $config or $context to the callback functions.
namespace HTMLPurifier;

/**
 * Handles referencing and derefencing character entities
 */
class EntityParser
{
    /**
     * Reference to entity lookup table.
     *
     * @var EntityLookup
     */
    protected $entityLookup;

    /**
     * Callback regex string for entities in text.
     *
     * @var string
     */
    protected $textEntitiesRegex;

    /**
     * Callback regex string for entities in attributes.
     *
     * @var string
     */
    protected $attrEntitiesRegex;

    /**
     * Tests if the beginning of a string is a semi-optional regex
     */
    protected $semiOptionalPrefixRegex;

    public function __construct()
    {
        // From
        // http://stackoverflow.com/questions/15532252/why-is-reg-being-rendered-as-without-the-bounding-semicolon
        $semi_optional = 'quot|QUOT|lt|LT|gt|GT|amp|AMP|AElig|Aacute|Acirc|Agrave|Aring|Atilde|Auml|COPY|Ccedil|ETH|Eacute|Ecirc|Egrave|Euml|Iacute|Icirc|Igrave|Iuml|Ntilde|Oacute|Ocirc|Ograve|Oslash|Otilde|Ouml|REG|THORN|Uacute|Ucirc|Ugrave|Uuml|Yacute|aacute|acirc|acute|aelig|agrave|aring|atilde|auml|brvbar|ccedil|cedil|cent|copy|curren|deg|divide|eacute|ecirc|egrave|eth|euml|frac12|frac14|frac34|iacute|icirc|iexcl|igrave|iquest|iuml|laquo|macr|micro|middot|nbsp|not|ntilde|oacute|ocirc|ograve|ordf|ordm|oslash|otilde|ouml|para|plusmn|pound|raquo|reg|sect|shy|sup1|sup2|sup3|szlig|thorn|times|uacute|ucirc|ugrave|uml|uuml|yacute|yen|yuml'; //phpcs:ignore

        // NB: three empty captures to put the fourth match in the right
        // place
        $this->semiOptionalPrefixRegex = "/&()()()($semi_optional)/";

        $this->textEntitiesRegex =
            '/&(?:' .
            // hex
            '[#]x([a-fA-F0-9]+);?|' .
            // dec
            '[#]0*(\d+);?|' .
            // string (mandatory semicolon)
            // NB: order matters: match semicolon preferentially
            '([A-Za-z_:][A-Za-z0-9.\-_:]*);|' .
            // string (optional semicolon)
            "($semi_optional)" .
            ')/';

        $this->attrEntitiesRegex =
            '/&(?:' .
            // hex
            '[#]x([a-fA-F0-9]+);?|' .
            // dec
            '[#]0*(\d+);?|' .
            // string (mandatory semicolon)
            // NB: order matters: match semicolon preferentially
            '([A-Za-z_:][A-Za-z0-9.\-_:]*);|' .
            // string (optional semicolon)
            // don't match if trailing is equals or alphanumeric (URL
            // like)
            "($semi_optional)(?![=;A-Za-z0-9])" .
            ')/';
    }

    /**
     * Substitute entities with the parsed equivalents.  Use this on
     * textual data in an HTML document (as opposed to attributes.)
     *
     * @param string $string String to have entities parsed.
     *
     * @return string Parsed string.
     */
    public function substituteTextEntities(string $string): string
    {
        return preg_replace_callback(
            $this->textEntitiesRegex,
            [$this, 'entityCallback'],
            $string
        );
    }

    /**
     * Substitute entities with the parsed equivalents.  Use this on
     * attribute contents in documents.
     *
     * @param string $string String to have entities parsed.
     *
     * @return string Parsed string.
     */
    public function substituteAttrEntities(string $string): string
    {
        return preg_replace_callback(
            $this->attrEntitiesRegex,
            [$this, 'entityCallback'],
            $string
        );
    }

    /**
     * Callback function for substituteNonSpecialEntities() that does the work.
     *
     * @param array $matches PCRE matches array, with 0 the entire match, and
     *                       either index 1, 2 or 3 set with a hex value, dec value,
     *                       or string (respectively).
     *
     * @return string Replacement string.
     */

    protected function entityCallback(array $matches): string
    {
        $entity = $matches[0];
        $hex_part = @$matches[1];
        $dec_part = @$matches[2];
        $named_part = empty($matches[3]) ? (empty($matches[4]) ? '' : $matches[4]) : $matches[3];
        if ($hex_part !== null && $hex_part !== '') {
            return Encoder::unichr(hexdec($hex_part));
        }

        if ($dec_part !== null && $dec_part !== '') {
            return Encoder::unichr((int)$dec_part);
        }

        if (!$this->entityLookup) {
            $this->entityLookup = EntityLookup::instance();
        }

        if (isset($this->entityLookup->table[$named_part])) {
            return $this->entityLookup->table[$named_part];
        }

        // exact match didn't match anything, so test if
        // any of the semicolon optional match the prefix.
        // Test that this is an EXACT match is important to
        // prevent infinite loop
        if (!empty($matches[3])) {
            return preg_replace_callback(
                $this->semiOptionalPrefixRegex,
                [$this, 'entityCallback'],
                $entity
            );
        }

        return $entity;
    }

    // LEGACY CODE BELOW

    /**
     * Callback regex string for parsing entities.
     *
     * @var string
     */
    protected $substituteEntitiesRegex = '/&(?:[#]x([a-fA-F0-9]+)|[#]0*(\d+)|([A-Za-z_:][A-Za-z0-9.\-_:]*));?/';
    //                                       1. hex                2. dec     3. string (XML style)

    /**
     * Decimal to parsed string conversion table for special entities.
     *
     * @var array
     */
    protected $specialDec2str = [
        34 => '"',
        38 => '&',
        39 => "'",
        60 => '<',
        62 => '>'
    ];

    /**
     * Stripped entity names to decimal conversion table for special entities.
     *
     * @var array
     */
    protected $specialEnt2dec = [
        'quot' => 34,
        'amp' => 38,
        'lt' => 60,
        'gt' => 62
    ];

    /**
     * Substitutes non-special entities with their parsed equivalents. Since
     * running this whenever you have parsed character is t3h 5uck, we run
     * it before everything else.
     *
     * @param string $string String to have non-special entities parsed.
     *
     * @return string Parsed string.
     */
    public function substituteNonSpecialEntities(string $string): string
    {
        // it will try to detect missing semicolons, but don't rely on it
        return preg_replace_callback(
            $this->substituteEntitiesRegex,
            [$this, 'nonSpecialEntityCallback'],
            $string
        );
    }

    /**
     * Callback function for substituteNonSpecialEntities() that does the work.
     *
     * @param array $matches PCRE matches array, with 0 the entire match, and
     *                       either index 1, 2 or 3 set with a hex value, dec value,
     *                       or string (respectively).
     *
     * @return string Replacement string.
     */

    protected function nonSpecialEntityCallback(array $matches): string
    {
        // replaces all but big five
        $entity = $matches[0];
        $is_num = (@$matches[0][1] === '#');
        if ($is_num) {
            $is_hex = (@$entity[2] === 'x');
            $code = $is_hex ? hexdec($matches[1]) : (int)$matches[2];
            // abort for special characters
            if (isset($this->specialDec2str[$code])) {
                return $entity;
            }

            return Encoder::unichr($code);
        }

        if (isset($this->specialEnt2dec[$matches[3]])) {
            return $entity;
        }

        if (!$this->entityLookup) {
            $this->entityLookup = EntityLookup::instance();
        }

        return $this->entityLookup->table[$matches[3]] ?? $entity;
    }

    /**
     * Substitutes only special entities with their parsed equivalents.
     *
     * @notice We try to avoid calling this function because otherwise, it
     * would have to be called a lot (for every parsed section).
     *
     * @param string $string String to have non-special entities parsed.
     *
     * @return string Parsed string.
     */
    public function substituteSpecialEntities(string $string): string
    {
        return preg_replace_callback(
            $this->substituteEntitiesRegex,
            [$this, 'specialEntityCallback'],
            $string
        );
    }

    /**
     * Callback function for substituteSpecialEntities() that does the work.
     *
     * This callback has same syntax as nonSpecialEntityCallback().
     *
     * @param array $matches PCRE-style matches array, with 0 the entire match, and
     *                       either index 1, 2 or 3 set with a hex value, dec value,
     *                       or string (respectively).
     *
     * @return string Replacement string.
     */
    protected function specialEntityCallback(array $matches): string
    {
        $entity = $matches[0];
        $is_num = (@$matches[0][1] === '#');
        if ($is_num) {
            $is_hex = (@$entity[2] === 'x');
            $int = $is_hex ? hexdec($matches[1]) : (int)$matches[2];

            return $this->specialDec2str[$int] ?? $entity;
        }

        return isset($this->specialEnt2dec[$matches[3]]) ?
            $this->specialDec2str[$this->specialEnt2dec[$matches[3]]] :
            $entity;
    }
}
