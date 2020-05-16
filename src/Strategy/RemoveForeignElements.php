<?php

declare(strict_types=1);

namespace HTMLPurifier\Strategy;

use HTMLPurifier\Context;
use HTMLPurifier\AttrValidator;
use HTMLPurifier\ElementDef;
use HTMLPurifier\Generator;
use HTMLPurifier\Strategy;
use HTMLPurifier\Token;
use HTMLPurifier\Token\End;
use HTMLPurifier\Token\Start;
use \HTMLPurifier\Config;
use HTMLPurifier\Exception;
use HTMLPurifier\Token\Comment;
use HTMLPurifier\Token\EmptyToken;
use HTMLPurifier\Token\Text;

/**
 * Removes all unrecognized tags from the list of tokens.
 *
 * This strategy iterates through all the tokens and removes unrecognized
 * tokens. If a token is not recognized but a TagTransform is defined for
 * that element, the element will be transformed accordingly.
 */
class RemoveForeignElements extends Strategy
{
    /**
     * @param Token[] $tokens
     * @param Config  $config
     * @param Context $context
     *
     * @return Token[]
     * @throws Exception
     */
    public function execute($tokens, Config $config, Context $context): array
    {
        $definition = $config->getHTMLDefinition();
        $generator = new Generator($config, $context);
        $result = [];

        $escape_invalid_tags = $config->get('Core.EscapeInvalidTags');
        $remove_invalid_img = $config->get('Core.RemoveInvalidImg');

        // currently only used to determine if comments should be kept
        $trusted = $config->get('HTML.Trusted');
        $comment_lookup = $config->get('HTML.AllowedComments');
        $comment_regexp = $config->get('HTML.AllowedCommentsRegexp');
        $check_comments = $comment_lookup !== [] || $comment_regexp !== null;

        $remove_script_contents = $config->get('Core.RemoveScriptContents');
        $hidden_elements = $config->get('Core.HiddenElements');

        // remove script contents compatibility
        if ($remove_script_contents === true) {
            $hidden_elements['script'] = true;
        } elseif ($remove_script_contents === false && isset($hidden_elements['script'])) {
            unset($hidden_elements['script']);
        }

        $attr_validator = new AttrValidator();

        // removes tokens until it reaches a closing tag with its value
        $remove_until = false;

        // converts comments into text tokens when this is equal to a tag name
        $textify_comments = false;

        $token = false;
        $context->register('CurrentToken', $token);

        $e = false;
        if ($config->get('Core.CollectErrors')) {
            $e =& $context->get('ErrorCollector');
        }

        foreach ($tokens as $token) {
            if ($remove_until && (empty($token->is_tag) || $token->name !== $remove_until)) {
                continue;
            }

            if (!empty($token->is_tag)) {
                // DEFINITION CALL

                // before any processing, try to transform the element
                if (isset($token->name) && isset($definition->info_tag_transform[$token->name])) {
                    $original_name = $token->name;
                    // there is a transformation for this tag
                    // DEFINITION CALL
                    $token = $definition->info_tag_transform[$token->name]->transform($token, $config, $context);
                    if ($e) {
                        $e->send(E_NOTICE, 'Strategy_RemoveForeignElements: Tag transform', $original_name);
                    }
                }

                if (isset($token->name) && isset($definition->info[$token->name])) {
                    // mostly everything's good, but
                    // we need to make sure required attributes are in order
                    if (($token instanceof Start || $token instanceof EmptyToken) 
                        && $definition->info[$token->name]->required_attr 
                        && ($token->name !== 'img' || $remove_invalid_img) // ensure config option still works
                    ) {
                        $attr_validator->validateToken($token, $config, $context);
                        $ok = true;

                        /**
                         * @psalm-suppress PossiblyNullArrayAccess
                         * @psalm-suppress PossiblyNullArrayOffset
                         */
                        foreach ($definition->info[$token->name]->required_attr as $name) {
                            if (!isset($token->attr[$name])) {
                                $ok = false;
                                break;
                            }
                        }

                        if (!$ok) {
                            if ($e) {
                                $e->send(
                                    E_ERROR,
                                    'Strategy_RemoveForeignElements: Missing required attribute',
                                    $name ?? ''
                                );
                            }

                            continue;
                        }

                        $token->armor['ValidateAttributes'] = true;
                    }

                    if (isset($hidden_elements[$token->name]) && $token instanceof Start) {
                        $textify_comments = $token->name;
                    } elseif ($token->name === $textify_comments && $token instanceof End) {
                        $textify_comments = false;
                    }

                } elseif ($escape_invalid_tags) {
                    // invalid tag, generate HTML representation and insert in
                    if ($e) {
                        $e->send(E_WARNING, 'Strategy_RemoveForeignElements: Foreign element to text');
                    }

                    $token = new Text(
                        $generator->generateFromToken($token)
                    );
                } else {
                    // check if we need to destroy all of the tag's children
                    // CAN BE GENERICIZED
                    if (isset($hidden_elements[$token->name])) {
                        if ($token instanceof Start) {
                            $remove_until = $token->name;
                        } elseif ($token instanceof EmptyToken) {
                            // do nothing: we're still looking
                        } else {
                            $remove_until = false;
                        }

                        if ($e) {
                            $e->send(E_ERROR, 'Strategy_RemoveForeignElements: Foreign meta element removed');
                        }
                    } else {
                        if ($e) {
                            $e->send(E_ERROR, 'Strategy_RemoveForeignElements: Foreign element removed');
                        }
                    }

                    continue;
                }
            } elseif ($token instanceof Comment) {
                // textify comments in script tags when they are allowed
                if ($textify_comments !== false) {
                    $data = $token->data;
                    $token = new Text($data);
                } elseif ($trusted || $check_comments) {
                    // always cleanup comments
                    $trailing_hyphen = false;

                    // perform check whether or not there's a trailing hyphen
                    if ($e && substr($token->data, -1) === '-') {
                        $trailing_hyphen = true;
                    }

                    $token->data = rtrim($token->data, '-');
                    $found_double_hyphen = false;

                    while (strpos($token->data, '--') !== false) {
                        $found_double_hyphen = true;
                        $token->data = str_replace('--', '-', $token->data);
                    }

                    if ($trusted || !empty($comment_lookup[trim($token->data)]) 
                        || ($comment_regexp !== null && preg_match($comment_regexp, trim($token->data)))
                    ) {
                        // OK good
                        if ($e) {
                            if ($trailing_hyphen) {
                                $e->send(
                                    E_NOTICE,
                                    'Strategy_RemoveForeignElements: Trailing hyphen in comment removed'
                                );
                            }
                            if ($found_double_hyphen) {
                                $e->send(E_NOTICE, 'Strategy_RemoveForeignElements: Hyphens in comment collapsed');
                            }
                        }
                    } else {
                        if ($e) {
                            $e->send(E_NOTICE, 'Strategy_RemoveForeignElements: Comment removed');
                        }
                        continue;
                    }
                } else {
                    // strip comments
                    if ($e) {
                        $e->send(E_NOTICE, 'Strategy_RemoveForeignElements: Comment removed');
                    }
                    continue;
                }
            } elseif ($token instanceof Text) {
            } else {
                continue;
            }

            $result[] = $token;
        }

        if ($remove_until && $e) {
            // we removed tokens until the end, throw error
            $e->send(E_ERROR, 'Strategy_RemoveForeignElements: Token removed to end', $remove_until);
        }

        $context->destroy('CurrentToken');

        return $result;
    }
}
