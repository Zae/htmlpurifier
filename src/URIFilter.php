<?php

declare(strict_types=1);

namespace HTMLPurifier;

/**
 * Chainable filters for custom URI processing.
 *
 * These filters can perform custom actions on a URI filter object,
 * including transformation or blacklisting.  A filter named Foo
 * must have a corresponding configuration directive %URI.Foo,
 * unless always_load is specified to be true.
 *
 * The following contexts may be available while URIFilters are being
 * processed:
 *
 *      - EmbeddedURI: true if URI is an embedded resource that will
 *        be loaded automatically on page load
 *      - CurrentToken: a reference to the token that is currently
 *        being processed
 *      - CurrentAttr: the name of the attribute that is currently being
 *        processed
 *      - CurrentCSSProperty: the name of the CSS property that is
 *        currently being processed (if applicable)
 *
 * @warning This filter is called before scheme object validation occurs.
 *          Make sure, if you require a specific scheme object, you
 *          you check that it exists. This allows filters to convert
 *          proprietary URI schemes into regular ones.
 */
abstract class URIFilter
{
    /**
     * Unique identifier of filter.
     *
     * @var string
     */
    public $name;

    /**
     * True if this filter should be run after scheme validation.
     *
     * @var bool
     */
    public $post = false;

    /**
     * True if this filter should always be loaded.
     * This permits a filter to be named Foo without the corresponding
     * %URI.Foo directive existing.
     *
     * @var bool
     */
    public $always_load = false;

    /**
     * Performs initialization for the filter.  If the filter returns
     * false, this means that it shouldn't be considered active.
     *
     * @param Config $config
     *
     * @return bool
     */
    public function prepare(Config $config)
    {
        return true;
    }

    /**
     * Filter a URI object
     *
     * @param URI     $uri Reference to URI object variable
     * @param Config  $config
     * @param Context $context
     *
     * @return bool Whether or not to continue processing: false indicates
     *         URL is no good, true indicates continue processing. Note that
     *         all changes are committed directly on the URI object
     */
    abstract public function filter(URI &$uri, Config $config, Context $context);
}
