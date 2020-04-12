<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit;

use HTMLPurifier_Config;
use HTMLPurifier\Context;
use HTMLPurifier_URIScheme;
use HTMLPurifier_URIScheme_http;
use HTMLPurifier_URISchemeRegistry;
use Mockery;

/**
 * Class URISchemeRegistryTest
 *
 * @package HTMLPurifier\Tests\Unit
 */
class URISchemeRegistryTest extends TestCase
{
    /**
     * @test
     */
    public function test(): void
    {
        $config = HTMLPurifier_Config::create([
            'URI.AllowedSchemes' => 'http, telnet',
            'URI.OverrideAllowedSchemes' => true
        ]);
        $context = new Context();

        $registry = new HTMLPurifier_URISchemeRegistry();
        static::assertInstanceOf(HTMLPurifier_URIScheme_http::class, $registry->getScheme('http', $config, $context));

        $scheme_http = Mockery::mock(HTMLPurifier_URIScheme::class);
        $scheme_telnet = Mockery::mock(HTMLPurifier_URIScheme::class);
        $scheme_foobar = Mockery::mock(HTMLPurifier_URIScheme::class);

        // register a new scheme
        $registry->register('telnet', $scheme_telnet);
        static::assertEquals($scheme_telnet, $registry->getScheme('telnet', $config, $context));

        // overload a scheme, this is FINAL (forget about defaults)
        $registry->register('http', $scheme_http);
        static::assertEquals($scheme_http, $registry->getScheme('http', $config, $context));

        // when we register a scheme, it's automatically allowed
        $registry->register('foobar', $scheme_foobar);
        static::assertEquals($scheme_foobar, $registry->getScheme('foobar', $config, $context));

        // now, test when overriding is not allowed
        $config = HTMLPurifier_Config::create([
            'URI.AllowedSchemes' => 'http, telnet',
            'URI.OverrideAllowedSchemes' => false
        ]);

        static::assertNull($registry->getScheme('foobar', $config, $context));

        // scheme not allowed and never registered
        static::assertNull($registry->getScheme('ftp', $config, $context));
    }
}
