<?php

declare(strict_types=1);

namespace HTMLPurifier\ConfigSchema;

use HTMLPurifier\Log;
use HTMLPurifier\VarParser\Native;
use HTMLPurifier\VarParserException;
use HTMLPurifier\VarParser;
use HTMLPurifier\StringHashParser;
use HTMLPurifier\StringHash;
use HTMLPurifier\ConfigSchema\Interchange\Directive;
use HTMLPurifier\ConfigSchema\Interchange\Id;

/**
 * Class InterchangeBuilder
 *
 * @package HTMLPurifier\ConfigSchema
 */
class InterchangeBuilder
{
    /**
     * Used for processing DEFAULT, nothing else.
     *
     * @var VarParser
     */
    protected $varParser;

    /**
     * @param VarParser|null $varParser
     */
    public function __construct(VarParser $varParser = null)
    {
        $this->varParser = $varParser ?: new Native();
    }

    /**
     * @param string|null $dir
     *
     * @return Interchange
     * @throws Exception
     */
    public static function buildFromDirectory(string $dir = null): Interchange
    {
        $builder = new InterchangeBuilder();
        $interchange = new Interchange();

        return $builder->buildDir($interchange, $dir);
    }

    /**
     * @param Interchange $interchange
     * @param string|null $dir
     *
     * @return Interchange
     * @throws Exception
     */
    public function buildDir(
        Interchange $interchange,
        string $dir = null
    ): Interchange {
        if (!$dir) {
            $dir = HTMLPURIFIER_PREFIX . '/ConfigSchema/schema';
        }

        if (file_exists($dir . '/info.ini')) {
            $info = parse_ini_file($dir . '/info.ini');
            $interchange->name = $info['name'];
        }

        $files = [];
        $dh = opendir($dir);
        while (($file = readdir($dh)) !== false) {
            if (!$file || $file[0] === '.' || strrchr($file, '.') !== '.txt') {
                continue;
            }

            $files[] = $file;
        }
        closedir($dh);

        sort($files);
        foreach ($files as $file) {
            $this->buildFile($interchange, $dir . '/' . $file);
        }

        return $interchange;
    }

    /**
     * @param Interchange $interchange
     * @param string      $file
     *
     * @throws Exception
     */
    public function buildFile(Interchange $interchange, string $file): void
    {
        $parser = new StringHashParser();
        $this->build(
            $interchange,
            new StringHash($parser->parseFile($file))
        );
    }

    /**
     * Builds an interchange object based on a hash.
     *
     * @param Interchange $interchange HTMLPurifier_ConfigSchema_Interchange object to build
     * @param StringHash  $hash        source data
     *
     * @throws Exception
     */
    public function build(Interchange $interchange, StringHash $hash): void
    {
        // TODO: Do we want to force users to provide a StringHash or do we change the API to allow
        //  other stuff as well and we create the StringHash ourselves?
//        if (!$hash instanceof StringHash) {
//            $hash = new StringHash($hash);
//        }

        if (!isset($hash['ID'])) {
            throw new Exception('Hash does not have any ID');
        }

        if (strpos($hash['ID'], '.') === false) {
            if (count($hash) === 2 && isset($hash['DESCRIPTION'])) {
                $hash->offsetGet('DESCRIPTION'); // prevent complaining
            } else {
                throw new Exception('All directives must have a namespace');
            }
        } else {
            $this->buildDirective($interchange, $hash);
        }

        $this->findUnused($hash);
    }

    /**
     * @param Interchange $interchange
     * @param StringHash  $hash
     *
     * @throws Exception
     */
    public function buildDirective(
        Interchange $interchange,
        StringHash $hash
    ): void {
        $directive = new Directive();

        // These are required elements:
        $directive->id = $this->id($hash->offsetGet('ID'));
        $id = $directive->id->toString(); // convenience

        if (isset($hash['TYPE'])) {
            $type = explode('/', $hash->offsetGet('TYPE'));
            if (isset($type[1])) {
                $directive->typeAllowsNull = true;
            }
            $directive->type = $type[0];
        } else {
            throw new Exception("TYPE in directive hash '$id' not defined");
        }

        if (isset($hash['DEFAULT'])) {
            try {
                $directive->default = $this->varParser->parse(
                    $hash->offsetGet('DEFAULT'),
                    $directive->type,
                    $directive->typeAllowsNull
                );
            } catch (VarParserException $e) {
                throw new Exception($e->getMessage() . " in DEFAULT in directive hash '$id'");
            }
        }

        if (isset($hash['DESCRIPTION'])) {
            $directive->description = $hash->offsetGet('DESCRIPTION');
        }

        if (isset($hash['ALLOWED'])) {
            $directive->allowed = $this->lookup($this->evalArray($hash->offsetGet('ALLOWED')));
        }

        if (isset($hash['VALUE-ALIASES'])) {
            $directive->valueAliases = $this->evalArray($hash->offsetGet('VALUE-ALIASES'));
        }

        if (isset($hash['ALIASES'])) {
            $raw_aliases = trim($hash->offsetGet('ALIASES'));
            $aliases = preg_split('/\s*,\s*/', $raw_aliases);
            foreach ($aliases as $alias) {
                $directive->aliases[] = $this->id($alias);
            }
        }

        if (isset($hash['VERSION'])) {
            $directive->version = $hash->offsetGet('VERSION');
        }

        if (isset($hash['DEPRECATED-USE'])) {
            $directive->deprecatedUse = $this->id($hash->offsetGet('DEPRECATED-USE'));
        }

        if (isset($hash['DEPRECATED-VERSION'])) {
            $directive->deprecatedVersion = $hash->offsetGet('DEPRECATED-VERSION');
        }

        if (isset($hash['EXTERNAL'])) {
            $directive->external = preg_split('/\s*,\s*/', trim($hash->offsetGet('EXTERNAL')));
        }

        $interchange->addDirective($directive);
    }

    /**
     * Evaluates an array PHP code string without array() wrapper
     *
     * @param string $contents
     *
     * @return mixed
     */
    protected function evalArray(string $contents)
    {
        return eval("return [{$contents}];");
    }

    /**
     * Converts an array list into a lookup array.
     *
     * @param array $array
     *
     * @return array
     */
    protected function lookup(array $array): array
    {
        $ret = [];
        foreach ($array as $val) {
            $ret[$val] = true;
        }

        return $ret;
    }

    /**
     * Convenience function that creates an HTMLPurifier_ConfigSchema_Interchange_Id
     * object based on a string Id.
     *
     * @param string $id
     *
     * @return Id
     */
    protected function id(string $id): Id
    {
        return Id::make($id);
    }

    /**
     * Triggers errors for any unused keys passed in the hash; such keys
     * may indicate typos, missing values, etc.
     *
     * @param StringHash $hash Hash to check.
     */
    protected function findUnused(StringHash $hash): void
    {
        $accessed = $hash->getAccessed();
        foreach ($hash as $k => $v) {
            if (!isset($accessed[$k])) {
                Log::notice("String hash key '$k' not used by builder");
            }
        }
    }
}
