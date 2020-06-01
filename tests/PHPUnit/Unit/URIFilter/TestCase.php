<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit\URIFilter;

use HTMLPurifier\Tests\Unit\UriTestCase;

/**
 * Class TestCase
 *
 * @package HTMLPurifier\Tests\Unit\URIFilter
 */
abstract class TestCase extends UriTestCase
{
    /**
     * @param      $uri
     * @param bool $expect_uri
     */
    protected function assertFiltering($uri, $expect_uri = true): void
    {
        $this->prepareURI($uri, $expect_uri);
        $this->filter->prepare($this->config, $this->context);
        $result = $this->filter->filter($uri, $this->config, $this->context);
        $this->assertEitherFailOrIdentical($result, $uri, $expect_uri);
    }
}
