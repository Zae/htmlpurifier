<?php

declare(strict_types=1);

namespace HTMLPurifier\Injector;

use HTMLPurifier\Injector;
use HTMLPurifier\Token\End;
use HTMLPurifier\Token\Start;
use HTMLPurifier\Token\Text;

/**
 * Injector that converts http, https and ftp text URLs to actual links.
 */
class Linkify extends Injector
{
    /**
     * @type string
     */
    public $name = 'Linkify';

    /**
     * @type array
     */
    public $needed = ['a' => ['href']];

    /**
     * @param Text $token
     * @param-out Text|list<Start|End|Text> $token
     * @return void
     */
    public function handleText(Text &$token)
    {
        if (!$this->allowsElement('a')) {
            return;
        }

        if (strpos($token->data, '://') === false) {
            // our really quick heuristic failed, abort
            // this may not work so well if we want to match things like
            // "google.com", but then again, most people don't
            return;
        }

        // there is/are URL(s). Let's split the string.
        // We use this regex:
        // https://gist.github.com/gruber/249502
        // but with @cscott's backtracking fix and also
        // the Unicode characters un-Unicodified.
        $bits = preg_split(
            '/\\b((?:[a-z][\\w\\-]+:(?:\\/{1,3}|[a-z0-9%])|www\\d{0,3}[.]|[a-z0-9.\\-]+[.][a-z]{2,4}\\/)(?:[^\\s()<>]|\\((?:[^\\s()<>]|(?:\\([^\\s()<>]+\\)))*\\))+(?:\\((?:[^\\s()<>]|(?:\\([^\\s()<>]+\\)))*\\)|[^\\s`!()\\[\\]{};:\'".,<>?\x{00ab}\x{00bb}\x{201c}\x{201d}\x{2018}\x{2019}]))/iu',
            $token->data, -1, PREG_SPLIT_DELIM_CAPTURE);

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
                $token[] = new Start('a', ['href' => $bits[$i]]);
                $token[] = new Text($bits[$i]);
                $token[] = new End('a');
            }
        }
    }
}
