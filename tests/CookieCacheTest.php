<?php

declare(strict_types=1);

namespace ColinODell\CookieCache\Tests;

use ColinODell\CookieCache\CookieCache;
use PHPUnit\Framework\TestCase;
use Psr\SimpleCache\InvalidArgumentException;

/**
 * @runTestsInSeparateProcesses
 */
final class CookieCacheTest extends TestCase
{
    private CookieCache $cache;

    public function setUp(): void
    {
        $this->cache = new CookieCache();
        $_COOKIE     = [];
    }

    public function testGet(): void
    {
        $_COOKIE['_c.foo'] = '{"foo":true}';

        self::assertSame(['foo' => true], $this->cache->get('foo'));
    }

    public function testGetWhenNotExists(): void
    {
        self::assertSame('some_default', $this->cache->get('foo', 'some_default'));
    }

    public function testGetWhenNotJsonDecodable(): void
    {
        $_COOKIE['_c.foo'] = '{{{';

        $result = $this->cache->get('foo', 'some_default');

        self::assertSame('some_default', $result);
    }

    public function testSet(): void
    {
        $result = $this->cache->set('foo', 'bar');

        self::assertTrue($result);
        self::assertSame('"bar"', $_COOKIE['_c.foo']);
    }

    public function testSetOverwritesExisting(): void
    {
        $_COOKIE['_c.foo'] = '"bar"';

        $result = $this->cache->set('foo', 'baz');

        self::assertTrue($result);
        self::assertSame('"baz"', $_COOKIE['_c.foo']);
    }

    public function testSetWhenNotJsonEncodable(): void
    {
        self::expectException(InvalidArgumentException::class);
        $this->cache->set('foo', "\xc3\x28");
    }

    public function testSetWithTtl(): void
    {
        $result = $this->cache->set('foo', 'bar', 1);

        self::assertTrue($result);
        self::assertSame('"bar"', $_COOKIE['_c.foo']);
    }

    public function testSetWithDateInterval(): void
    {
        $result = $this->cache->set('foo', 'bar', new \DateInterval('P1D'));

        self::assertTrue($result);
        self::assertSame('"bar"', $_COOKIE['_c.foo']);
    }

    public function testDelete(): void
    {
        $_COOKIE['_c.foo'] = '"bar"';

        $result = $this->cache->delete('foo');

        self::assertTrue($result);
        self::assertArrayNotHasKey('_c.foo', $_COOKIE);
    }

    public function testDeleteWhenNotExists(): void
    {
        $result = $this->cache->delete('foo');

        self::assertTrue($result);
    }

    public function testClear(): void
    {
        $_COOKIE['_c.foo']    = '"foo"';
        $_COOKIE['_c.bar']    = '"bar"';
        $_COOKIE['unrelated'] = 'unrelated';

        $result = $this->cache->clear();

        self::assertTrue($result);
        self::assertArrayNotHasKey('_c.foo', $_COOKIE);
        self::assertArrayNotHasKey('_c.bar', $_COOKIE);
        self::assertArrayHasKey('unrelated', $_COOKIE);
    }

    public function testGetMultiple(): void
    {
        $_COOKIE['_c.foo'] = '"foo"';
        $_COOKIE['_c.bar'] = '"bar"';

        $result = \iterator_to_array($this->cache->getMultiple(['foo', 'bar']));

        self::assertSame(['foo' => 'foo', 'bar' => 'bar'], $result);
    }

    public function testSetMultiple(): void
    {
        $result = $this->cache->setMultiple(['foo' => 'foo', 'bar' => 'bar']);

        self::assertTrue($result);
        self::assertSame('"foo"', $_COOKIE['_c.foo']);
        self::assertSame('"bar"', $_COOKIE['_c.bar']);
    }

    public function testDeleteMultiple(): void
    {
        $_COOKIE['_c.foo'] = '"foo"';
        $_COOKIE['_c.bar'] = '"bar"';
        $_COOKIE['_c.baz'] = '"baz"';

        $result = $this->cache->deleteMultiple(['foo', 'bar']);

        self::assertTrue($result);
        self::assertArrayNotHasKey('_c.foo', $_COOKIE);
        self::assertArrayNotHasKey('_c.bar', $_COOKIE);
        self::assertArrayHasKey('_c.baz', $_COOKIE);
    }

    public function testHas(): void
    {
        $_COOKIE['_c.foo'] = '"foo"';

        self::assertTrue($this->cache->has('foo'));
        self::assertFalse($this->cache->has('bar'));
    }
}
