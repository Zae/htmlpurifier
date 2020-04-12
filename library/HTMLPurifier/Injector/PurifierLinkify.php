<?php

declare(strict_types=1);

use HTMLPurifier\Token\End;
use HTMLPurifier\Token\Start;

/**
 * Injector that converts configuration directive syntax %Namespace.Directive
 * to links
 */
class HTMLPurifier_Injector_PurifierLinkify extends HTMLPurifier_Injector
{
    /**
     * @type string
     */
    public $name = 'PurifierLinkify';

    /**
     * @type string
     */
    public $docURL;

    /**
     * @type array
     */
    public $needed = ['a' => ['href']];

    /**
     * @param HTMLPurifier_Config  $config
     * @param HTMLPurifier_Context $context
     *
     * @return string|bool
     * @throws HTMLPurifier_Exception
     */
    public function prepare(HTMLPurifier_Config $config, HTMLPurifier_Context $context)
    {
        $this->docURL = $config->get('AutoFormat.PurifierLinkify.DocURL');

        return parent::prepare($config, $context);
    }

    /**
     * @param HTMLPurifier_Token_Text $token
     */
    public function handleText(HTMLPurifier_Token_Text &$token)
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
        for ($i = 0, $c = count($bits), $l = false; $i < $c; $i++, $l = !$l) {
            if (!$l) {
                if ($bits[$i] === '') {
                    continue;
                }
                $token[] = new HTMLPurifier_Token_Text($bits[$i]);
            } else {
                $token[] = new Start(
                    'a',
                    ['href' => str_replace('%s', $bits[$i], $this->docURL)]
                );
                $token[] = new HTMLPurifier_Token_Text('%' . $bits[$i]);
                $token[] = new End('a');
            }
        }
    }
}
