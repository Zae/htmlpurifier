<?php

declare(strict_types=1);

namespace HTMLPurifier;

use HTMLPurifier\AttrDef\URI;
use HTMLPurifier\AttrDef\Text;
use HTMLPurifier\AttrDef\Lang;
use HTMLPurifier\AttrDef\Integer;
use HTMLPurifier\AttrDef\Enum;
use HTMLPurifier\AttrDef\Cloner;
use HTMLPurifier\AttrDef\HTML\Pixels;
use HTMLPurifier\AttrDef\HTML\Nmtokens;
use HTMLPurifier\AttrDef\HTML\MultiLength;
use HTMLPurifier\AttrDef\HTML\Boolean;
use HTMLPurifier\AttrDef\HTML\Classname;
use HTMLPurifier\AttrDef\HTML\Color;
use HTMLPurifier\AttrDef\HTML\FrameTarget;
use HTMLPurifier\AttrDef\HTML\Length;
use HTMLPurifier\AttrDef\HTML\ID;

/**
 * Provides lookup array of attribute types to HTMLPurifier_AttrDef objects
 */
class AttrTypes
{
    /**
     * Lookup array of attribute string identifiers to concrete implementations.
     *
     * @var AttrDef[]
     */
    protected $info = [];

    /**
     * Constructs the info array, supplying default implementations for attribute
     * types.
     */
    public function __construct()
    {
        // XXX This is kind of poor, since we don't actually /clone/
        // instances; instead, we use the supplied make() attribute. So,
        // the underlying class must know how to deal with arguments.
        // With the old implementation of Enum, that ignored its
        // arguments when handling a make dispatch, the IAlign
        // definition wouldn't work.

        // pseudo-types, must be instantiated via shorthand
        $this->info['Enum'] = new Enum();
        $this->info['Bool'] = new Boolean();

        $this->info['CDATA'] = new Text();
        $this->info['ID'] = new ID();
        $this->info['Length'] = new Length();
        $this->info['MultiLength'] = new MultiLength();
        $this->info['NMTOKENS'] = new Nmtokens();
        $this->info['Pixels'] = new Pixels();
        $this->info['Text'] = new Text();
        $this->info['URI'] = new URI();
        $this->info['LanguageCode'] = new Lang();
        $this->info['Color'] = new Color();
        $this->info['IAlign'] = self::makeEnum('top,middle,bottom,left,right');
        $this->info['LAlign'] = self::makeEnum('top,bottom,left,right');
        $this->info['FrameTarget'] = new FrameTarget();

        // unimplemented aliases
        $this->info['ContentType'] = new Text();
        $this->info['ContentTypes'] = new Text();
        $this->info['Charsets'] = new Text();
        $this->info['Character'] = new Text();

        // "proprietary" types
        $this->info['Class'] = new Classname();

        // number is really a positive integer (one or more digits)
        // FIXME: ^^ not always, see start and value of list items
        $this->info['Number'] = new Integer(false, false, true);
    }

    /**
     * @param string $in
     * @return Cloner
     */
    private static function makeEnum(string $in): Cloner
    {
        return new Cloner(new Enum(explode(',', $in)));
    }

    /**
     * Retrieves a type
     *
     * @param string $type String type name
     *
     * @return AttrDef Object AttrDef for type
     * @throws Exception
     */
    public function get(string $type): ?AttrDef
    {
        // determine if there is any extra info tacked on
        if (strpos($type, '#') !== false) {
            [$type, $string] = explode('#', $type, 2);
        } else {
            $string = '';
        }

        if (!isset($this->info[$type])) {
            throw new Exception("Cannot retrieve undefined attribute type {$type}");
        }

        return $this->info[$type]->make($string);
    }

    /**
     * Sets a new implementation for a type
     *
     * @param string  $type String type name
     * @param AttrDef $impl Object AttrDef for type
     */
    public function set(string $type, AttrDef $impl): void
    {
        $this->info[$type] = $impl;
    }
}
