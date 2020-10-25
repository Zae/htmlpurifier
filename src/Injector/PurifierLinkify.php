<?php

declare(strict_types=1);

namespace HTMLPurifier\Injector;

use HTMLPurifier\Context;
use HTMLPurifier\Injector;
use HTMLPurifier\Token\End;
use HTMLPurifier\Token\Start;
use HTMLPurifier\Config;
use HTMLPurifier\Token\Text;

/**
 * Injector that converts configuration directive syntax %Namespace.Directive
 * to links
 */
class PurifierLinkify extends Injector
{
    /**
     * @var string
     */
    public $name = 'PurifierLinkify';

    /**
     * @var string
     */
    public $docURL = '';

    /**
     * @var array
     */
    public $needed = ['a' => ['href']];

    /**
     * @param Config  $config
     * @param Context $context
     *
     * @return string|bool
     * @throws \HTMLPurifier\Exception
     */
    public function prepare(Config $config, Context $context)
    {
        $this->docURL = $config->get('AutoFormat.PurifierLinkify.DocURL');

        return parent::prepare($config, $context);
    }

    /**
     * @param Text $token
     *
     * @return void
     *
     * @param-out Text|list<Start|End|Text> $token
     */
    public function handleText(Text &$token): void
    {
        if (!$this->allowsElement('a')) {
            return;
        }
        if (strpos($token->data, '%') === false) {
            return;
        }

        $bits = preg_split('#%([a-z0-9]+\.[a-z0-9]+)#Si', $token->data, -1, PREG_SPLIT_DELIM_CAPTURE);
        $token = [];

        // $i = index
        // $c = count
        // $l = is link
        for ($i = 0, $c = \count($bits), $l = false; $i < $c; $i++, $l = !$l) {
            if (!$l) {
                if ($bits[$i] === '') {
                    continue;
                }
                $token[] = new Text($bits[$i]);
            } else {
                $token[] = new Start(
                    'a',
                    ['href' => str_replace('%s', $bits[$i], $this->docURL)]
                );
                $token[] = new Text('%' . $bits[$i]);
                $token[] = new End('a');
            }
        }
    }
}
