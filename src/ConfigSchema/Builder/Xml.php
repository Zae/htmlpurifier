<?php

declare(strict_types=1);

namespace HTMLPurifier\ConfigSchema\Builder;

use HTMLPurifier\ConfigSchema\Interchange;
use HTMLPurifier\ConfigSchema\Interchange\Directive;
use HTMLPurifier\ConfigSchema\Interchange\Id;
use HTMLPurifier\Exception;
use HTMLPurifier\HTMLPurifier;
use XMLWriter;

use function is_null;

/**
 * Converts HTMLPurifier_ConfigSchema_Interchange to an XML format,
 * which can be further processed to generate documentation.
 */
class Xml extends XMLWriter
{
    /**
     * @var Interchange|null
     */
    protected $interchange;

    /**
     * @var string
     */
    private $namespace = '';

    /**
     * @param string $html
     *
     * @throws Exception
     */
    protected function writeHTMLDiv(string $html): void
    {
        $this->startElement('div');

        $purifier = HTMLPurifier::getInstance();
        $html = $purifier->purify($html);
        $this->writeAttribute('xmlns', 'http://www.w3.org/1999/xhtml');

        if (!is_null($html)) {
            $this->writeRaw($html);
        }

        $this->endElement(); // div
    }

    /**
     * @param mixed $var
     *
     * @return string
     */
    protected function export($var): string
    {
        if ($var === []) {
            return 'array()';
        }

        return var_export($var, true);
    }

    /**
     * @param Interchange $interchange
     * @throws Exception
     */
    public function build(Interchange $interchange): void
    {
        // global access, only use as last resort
        $this->interchange = $interchange;

        $this->setIndent(true);
        $this->startDocument('1.0', 'UTF-8');
        $this->startElement('configdoc');
        $this->writeElement('title', $interchange->name);

        foreach ($interchange->directives as $directive) {
            $this->buildDirective($directive);
        }

        if ($this->namespace) {
            $this->endElement();
        } // namespace

        $this->endElement(); // configdoc
        $this->flush();
    }

    /**
     * @param Directive $directive
     *
     * @throws Exception
     */
    public function buildDirective(Directive $directive): void
    {
        if (!$directive->id instanceof Id) {
            throw new Exception('Directive id is wrong type');
        }

        // Kludge, although I suppose having a notion of a "root namespace"
        // certainly makes things look nicer when documentation is built.
        // Depends on things being sorted.
        if (!$this->namespace || $this->namespace !== $directive->id->getRootNamespace()) {
            if ($this->namespace) {
                $this->endElement();
            } // namespace
            $this->namespace = $directive->id->getRootNamespace();
            $this->startElement('namespace');
            $this->writeAttribute('id', $this->namespace);
            $this->writeElement('name', $this->namespace);
        }

        $this->startElement('directive');
        $this->writeAttribute('id', $directive->id->toString());
        $this->writeElement('name', $directive->id->getDirective());

        $this->startElement('aliases');
        foreach ($directive->aliases as $alias) {
            $this->writeElement('alias', $alias->toString());
        }
        $this->endElement(); // aliases

        $this->startElement('constraints');
        if ($directive->version) {
            $this->writeElement('version', $directive->version);
        }
        $this->startElement('type');
        if ($directive->typeAllowsNull) {
            $this->writeAttribute('allow-null', 'yes');
        }
        $this->text($directive->type);
        $this->endElement(); // type
        if ($directive->allowed) {
            $this->startElement('allowed');
            foreach ($directive->allowed as $value => $x) {
                $this->writeElement('value', $value);
            }
            $this->endElement(); // allowed
        }
        $this->writeElement('default', $this->export($directive->default));
        $this->writeAttribute('xml:space', 'preserve');
        if ($directive->external) {
            $this->startElement('external');
            foreach ($directive->external as $project) {
                $this->writeElement('project', $project);
            }
            $this->endElement();
        }
        $this->endElement(); // constraints

        if ($directive->deprecatedVersion) {
            $this->startElement('deprecated');
            $this->writeElement('version', $directive->deprecatedVersion);
            $this->writeElement(
                'use',
                !is_null($directive->deprecatedUse) ? $directive->deprecatedUse->toString() : null
            );
            $this->endElement(); // deprecated
        }

        $this->startElement('description');
        $this->writeHTMLDiv($directive->description);
        $this->endElement(); // description

        $this->endElement(); // directive
    }
}
