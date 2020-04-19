<?php

declare(strict_types=1);

namespace HTMLPurifier\Injector;

use HTMLPurifier\Context;
use HTMLPurifier\AttrValidator;
use HTMLPurifier\Injector;
use HTMLPurifier\Token;
use HTMLPurifier\Token\End;
use HTMLPurifier\Token\Start;
use \HTMLPurifier\Config;
use HTMLPurifier\Exception;
use HTMLPurifier\Token\Text;

/**
 * Class HTMLPurifier\Injector\HTMLPurifier_Injector_RemoveEmpty
 */
class RemoveEmpty extends Injector
{
    /**
     * @type Context
     */
    private $context;

    /**
     * @type Config
     */
    private $config;

    /**
     * @type AttrValidator
     */
    private $attrValidator;

    /**
     * @type bool
     */
    private $removeNbsp;

    /**
     * @type bool
     */
    private $removeNbspExceptions;

    /**
     * Cached contents of %AutoFormat.RemoveEmpty.Predicate
     *
     * @type array
     */
    private $exclude;

    /**
     * @param Config  $config
     * @param Context $context
     *
     * @return void
     * @throws \HTMLPurifier\Exception
     */
    public function prepare(Config $config, Context $context): void
    {
        parent::prepare($config, $context);

        $this->config = $config;
        $this->context = $context;
        $this->removeNbsp = $config->get('AutoFormat.RemoveEmpty.RemoveNbsp');
        $this->removeNbspExceptions = $config->get('AutoFormat.RemoveEmpty.RemoveNbsp.Exceptions');
        $this->exclude = $config->get('AutoFormat.RemoveEmpty.Predicate');

        foreach ($this->exclude as $key => $attrs) {
            if (!\is_array($attrs)) {
                // HACK, see HTMLPurifier/Printer/ConfigForm.php
                $this->exclude[$key] = explode(';', $attrs);
            }
        }

        $this->attrValidator = new AttrValidator();
    }

    /**
     * @param Token $token
     */
    public function handleElement(Token &$token)
    {
        if (!$token instanceof Start) {
            return;
        }

        $next = false;
        $deleted = 1; // the current tag

        for ($i = \count($this->inputZipper->back) - 1; $i >= 0; $i--, $deleted++) {
            $next = $this->inputZipper->back[$i];
            if ($next instanceof Text) {
                if ($next->is_whitespace) {
                    continue;
                }

                if ($this->removeNbsp && !isset($this->removeNbspExceptions[$token->name])) {
                    $plain = str_replace("\xC2\xA0", '', $next->data);
                    $isWsOrNbsp = $plain === '' || ctype_space($plain);
                    if ($isWsOrNbsp) {
                        continue;
                    }
                }
            }

            break;
        }

        if (!$next || ($next instanceof End && $next->name === $token->name)) {
            $this->attrValidator->validateToken($token, $this->config, $this->context);
            $token->armor['ValidateAttributes'] = true;
            if (isset($this->exclude[$token->name])) {
                $r = true;
                foreach ($this->exclude[$token->name] as $elem) {
                    if (!isset($token->attr[$elem])) {
                        $r = false;
                    }
                }

                if ($r) {
                    return;
                }
            }

            if (isset($token->attr['id']) || isset($token->attr['name'])) {
                return;
            }

            $token = $deleted + 1;

            for ($b = 0, $c = \count($this->inputZipper->front); $b < $c; $b++) {
                $prev = $this->inputZipper->front[$b];
                if ($prev instanceof Text && $prev->is_whitespace) {
                    continue;
                }
                break;
            }

            // This is safe because we removed the token that triggered this.
            $this->rewindOffset($b + $deleted);

            return;
        }
    }
}
