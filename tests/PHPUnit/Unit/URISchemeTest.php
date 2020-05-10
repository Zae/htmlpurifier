<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit;

/**
 * Class URISchemeTest
 *
 * @package HTMLPurifier\Tests\Unit
 */
class URISchemeTest extends UriTestCase
{
    private $pngBase64  =
        'iVBORw0KGgoAAAANSUhEUgAAAAoAAAAKCAYAAACNMs+9AAAABGdBTUEAALGP'.
        'C/xhBQAAAAlwSFlzAAALEwAACxMBAJqcGAAAAAd0SU1FB9YGARc5KB0XV+IA'.
        'AAAddEVYdENvbW1lbnQAQ3JlYXRlZCB3aXRoIFRoZSBHSU1Q72QlbgAAAF1J'.
        'REFUGNO9zL0NglAAxPEfdLTs4BZM4DIO4C7OwQg2JoQ9LE1exdlYvBBeZ7jq'.
        'ch9//q1uH4TLzw4d6+ErXMMcXuHWxId3KOETnnXXV6MJpcq2MLaI97CER3N0'.
        'vr4MkhoXe0rZigAAAABJRU5ErkJggg==';

    /**
     * @param      $uri
     * @param bool $expect_uri
     */
    private function assertValidation($uri, $expect_uri = true): void
    {
        $this->prepareURI($uri, $expect_uri);
        $this->config->set('URI.AllowedSchemes', [$uri->scheme]);
        // convenience hack: the scheme should be explicitly specified
        $scheme = $uri->getSchemeObj($this->config, $this->context);
        $result = $scheme->validate($uri, $this->config, $this->context);
        $this->assertEitherFailOrIdentical($result, $uri, $expect_uri);
    }

    /**
     * @test
     */
    public function test_http_regular(): void
    {
        $this->assertValidation(
            'http://example.com/?s=q#fragment'
        );
    }

    /**
     * @test
     */
    public function test_http_uppercase(): void
    {
        $this->assertValidation(
            'http://example.com/FOO'
        );
    }

    /**
     * @test
     */
    public function test_http_removeDefaultPort(): void
    {
        $this->assertValidation(
            'http://example.com:80',
            'http://example.com'
        );
    }

    /**
     * @test
     */
    public function test_http_removeUserInfo(): void
    {
        $this->assertValidation(
            'http://bob@example.com',
            'http://example.com'
        );
    }

    /**
     * @test
     */
    public function test_http_preserveNonDefaultPort(): void
    {
        $this->assertValidation(
            'http://example.com:8080'
        );
    }

    /**
     * @test
     */
    public function test_https_regular(): void
    {
        $this->assertValidation(
            'https://user@example.com:443/?s=q#frag',
            'https://example.com/?s=q#frag'
        );
    }

    /**
     * @test
     */
    public function test_ftp_regular(): void
    {
        $this->assertValidation(
            'ftp://user@example.com/path'
        );
    }

    /**
     * @test
     */
    public function test_ftp_removeDefaultPort(): void
    {
        $this->assertValidation(
            'ftp://example.com:21',
            'ftp://example.com'
        );
    }

    /**
     * @test
     */
    public function test_ftp_removeQueryString(): void
    {
        $this->assertValidation(
            'ftp://example.com?s=q',
            'ftp://example.com'
        );
    }

    /**
     * @test
     */
    public function test_ftp_preserveValidTypecode(): void
    {
        $this->assertValidation(
            'ftp://example.com/file.txt;type=a'
        );
    }

    /**
     * @test
     */
    public function test_ftp_removeInvalidTypecode(): void
    {
        $this->assertValidation(
            'ftp://example.com/file.txt;type=z',
            'ftp://example.com/file.txt'
        );
    }

    /**
     * @test
     */
    public function test_ftp_encodeExtraSemicolons(): void
    {
        $this->assertValidation(
            'ftp://example.com/too;many;semicolons=1',
            'ftp://example.com/too%3Bmany%3Bsemicolons=1'
        );
    }

    /**
     * @test
     */
    public function test_news_regular(): void
    {
        $this->assertValidation(
            'news:gmane.science.linguistics'
        );
    }

    /**
     * @test
     */
    public function test_news_explicit(): void
    {
        $this->assertValidation(
            'news:642@eagle.ATT.COM'
        );
    }

    /**
     * @test
     */
    public function test_news_removeNonPathComponents(): void
    {
        $this->assertValidation(
            'news://user@example.com:80/rec.music?path=foo#frag',
            'news:/rec.music#frag'
        );
    }

    /**
     * @test
     */
    public function test_nntp_regular(): void
    {
        $this->assertValidation(
            'nntp://news.example.com/alt.misc/42#frag'
        );
    }

    /**
     * @test
     */
    public function test_nntp_removalOfRedundantOrUselessComponents(): void
    {
        $this->assertValidation(
            'nntp://user@news.example.com:119/alt.misc/42?s=q#frag',
            'nntp://news.example.com/alt.misc/42#frag'
        );
    }

    /**
     * @test
     */
    public function test_mailto_regular(): void
    {
        $this->assertValidation(
            'mailto:bob@example.com'
        );
    }

    /**
     * @test
     */
    public function test_mailto_removalOfRedundantOrUselessComponents(): void
    {
        $this->assertValidation(
            'mailto://user@example.com:80/bob@example.com?subject=Foo#frag',
            'mailto:/bob@example.com?subject=Foo#frag'
        );
    }

    /**
     * @test
     */
    public function test_tel_strip_punctuation(): void
    {
        $this->assertValidation(
            'tel:+1 (555) 555-5555', 'tel:+15555555555'
        );
    }

    /**
     * @test
     */
    public function test_tel_regular(): void
    {
        $this->assertValidation(
            'tel:+15555555555'
        );
    }

    /**
     * @test
     */
    public function test_tel_with_extension(): void
    {
        $this->assertValidation(
            'tel:+1-555-555-5555x123', 'tel:+15555555555x123'
        );
    }

    /**
     * @test
     */
    public function test_tel_no_plus(): void
    {
        $this->assertValidation(
            'tel:555-555-5555', 'tel:5555555555'
        );
    }

    /**
     * @test
     */
    public function test_tel_strip_letters(): void
    {
        $this->assertValidation(
            'tel:abcd1234',
            'tel:1234'
        );
    }

    /**
     * @test
     */
    public function test_data_png(): void
    {
        $this->assertValidation(
            'data:image/png;base64,'.$this->pngBase64
        );
    }

    /**
     * @test
     */
    public function test_data_malformed(): void
    {
        $this->assertValidation(
            'data:image/png;base64,vr4MkhoXJRU5ErkJggg==',
            false
        );
    }

    /**
     * @test
     */
    public function test_data_implicit(): void
    {
        $this->assertValidation(
            'data:base64,'.$this->pngBase64,
            'data:image/png;base64,'.$this->pngBase64
        );
    }

    /**
     * @test
     */
    public function test_file_basic(): void
    {
        $this->assertValidation(
            'file://user@MYCOMPUTER:12/foo/bar?baz#frag',
            'file://MYCOMPUTER/foo/bar#frag'
        );
    }

    /**
     * @test
     */
    public function test_file_local(): void
    {
        $this->assertValidation(
            'file:///foo/bar?baz#frag',
            'file:///foo/bar#frag'
        );
    }

    /**
     * @test
     */
    public function test_ftp_empty_host(): void
    {
        $this->assertValidation('ftp:///example.com', false);
    }

    /**
     * @test
     */
    public function test_data_bad_base64(): void
    {
        $this->assertValidation('data:image/png;base64,aGVsbG90aGVyZXk|', false);
    }

    /**
     * @test
     */
    public function test_data_too_short(): void
    {
        $this->assertValidation('data:image/png;base64,aGVsbG90aGVyZXk=', false);
    }
}
