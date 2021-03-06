<?php

declare(strict_types=1);

namespace Nyholm\Tests;

use PHPUnit\Framework\TestCase;

class AppendQueryStringTest extends TestCase
{
    public function provideBaseUrls()
    {
        yield ['https://foo.com', '', 'https://foo.com'];
        yield ['https://foo.com?', '', 'https://foo.com?'];
        yield ['https://foo.com', 'foo', 'https://foo.com?foo'];
        yield ['https://foo.com/', 'foo', 'https://foo.com/?foo'];
        yield ['https://foo.com', 'foo=bar', 'https://foo.com?foo=bar'];
        yield ['https://foo.com#aa', 'foo=bar', 'https://foo.com?foo=bar#aa'];
        yield ['https://foo.com/', 'foo=bar', 'https://foo.com/?foo=bar'];
        yield ['https://foo.com?', 'foo=bar', 'https://foo.com?foo=bar'];
        yield ['https://foo.com/page', 'foo=bar', 'https://foo.com/page?foo=bar'];
        yield ['https://foo.com/page/', 'foo=bar', 'https://foo.com/page/?foo=bar'];
        yield ['https://user:pass@foo.com/page/', 'foo=bar', 'https://user:pass@foo.com/page/?foo=bar'];
        yield ['https://user@foo.com/page/', 'foo=bar', 'https://user@foo.com/page/?foo=bar'];
        yield ['https://user:@foo.com/page/', 'foo=bar', 'https://user:@foo.com/page/?foo=bar'];
        yield ['https://foo.com/page/', 'foo=bar&biz=2', 'https://foo.com/page/?foo=bar&biz=2'];
    }

    public function provideModeIgnore()
    {
        yield from $this->provideBaseUrls();

        yield ['https://foo.com/page?aa=bb', 'foo=bar&biz=2', 'https://foo.com/page?aa=bb&foo=bar&biz=2'];
        yield ['https://foo.com/page?aa=bb#link', 'foo=bar&biz=2', 'https://foo.com/page?aa=bb&foo=bar&biz=2#link'];
        yield ['https://foo.com/page?aa=bb', 'foo=bar&aa=2', 'https://foo.com/page?aa=bb&foo=bar&aa=2'];
    }

    public function provideModeReplace()
    {
        yield from $this->provideBaseUrls();

        yield ['https://foo.com/page?aa=bb', 'foo=bar&biz=2', 'https://foo.com/page?aa=bb&foo=bar&biz=2'];
        yield ['https://foo.com/page?aa=bb#link', 'foo=bar&biz=2', 'https://foo.com/page?aa=bb&foo=bar&biz=2#link'];
        yield ['https://foo.com/page?aa=bb', 'foo=bar&aa=2', 'https://foo.com/page?aa=2&foo=bar'];
    }

    public function provideModeSkip()
    {
        yield from $this->provideBaseUrls();

        yield ['https://foo.com/page?aa=bb', 'foo=bar&biz=2', 'https://foo.com/page?foo=bar&biz=2&aa=bb'];
        yield ['https://foo.com/page?aa=bb#link', 'foo=bar&biz=2', 'https://foo.com/page?foo=bar&biz=2&aa=bb#link'];
        yield ['https://foo.com/page?aa=bb', 'foo=bar&aa=2', 'https://foo.com/page?foo=bar&aa=bb'];
    }

    /**
     * @dataProvider provideModeIgnore
     */
    public function testModeIgnore($url, $queryString, $expected)
    {
        $output = append_query_string($url, $queryString, APPEND_QUERY_STRING_IGNORE_DUPLICATE);
        $this->assertEquals($expected, $output);
    }

    /**
     * @dataProvider provideModeReplace
     */
    public function testModeReplace($url, $queryString, $expected)
    {
        $output = append_query_string($url, $queryString, APPEND_QUERY_STRING_REPLACE_DUPLICATE);
        $this->assertEquals($expected, $output);
    }

    /**
     * @dataProvider provideModeSkip
     */
    public function testModeSkip($url, $queryString, $expected)
    {
        $output = append_query_string($url, $queryString, APPEND_QUERY_STRING_SKIP_DUPLICATE);
        $this->assertEquals($expected, $output);
    }
}
