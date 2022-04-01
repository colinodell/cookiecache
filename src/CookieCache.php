<?php

declare(strict_types=1);

namespace ColinODell\CookieCache;

use Cake\Chronos\Chronos;
use Psr\SimpleCache\CacheInterface;

final class CookieCache implements CacheInterface
{
    private string $prefix = '_c.';

    public function get(string $key, mixed $default = null): mixed
    {
        $key = $this->prefix . $key;
        if (! isset($_COOKIE[$key])) {
            return $default;
        }

        try {
            return \json_decode($_COOKIE[$key], true, flags: \JSON_THROW_ON_ERROR);
        } catch (\JsonException) { // @phpstan-ignore-line
            return $default;
        }
    }

    public function set(string $key, mixed $value, \DateInterval|int|null $ttl = null): bool
    {
        $key = $this->prefix . $key;

        if ($ttl instanceof \DateInterval) {
            $expires = Chronos::now()->add($ttl)->timestamp;
        } elseif ($ttl === null) {
            $expires = 0;
        } else {
            $expires = Chronos::now()->addSeconds($ttl)->timestamp;
        }

        try {
            $value         = \json_encode($value, flags: \JSON_THROW_ON_ERROR);
            $_COOKIE[$key] = $value;

            return \setcookie($key, $value, $expires, '/', httponly: true);
        } catch (\JsonException $e) {
            throw new SerializationException('Could not encode value to JSON', 0, $e);
        }
    }

    public function delete(string $key): bool
    {
        $key = $this->prefix . $key;
        unset($_COOKIE[$key]);

        return @\setcookie($key, 'null', -1, '/', httponly: true);
    }

    public function clear(): bool
    {
        $result = true;

        foreach ($_COOKIE as $key => $value) {
            if (\str_starts_with($key, $this->prefix)) {
                $result = $result && $this->delete(\substr($key, \strlen($this->prefix)));
            }
        }

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function getMultiple(iterable $keys, mixed $default = null): iterable
    {
        foreach ($keys as $key) {
            yield $key => $this->get($key, $default);
        }
    }

    /**
     * @param iterable<mixed, mixed> $values
     */
    public function setMultiple(iterable $values, \DateInterval|int|null $ttl = null): bool
    {
        $result = true;
        foreach ($values as $key => $value) {
            $result = $result && $this->set($key, $value, $ttl);
        }

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function deleteMultiple(iterable $keys): bool
    {
        $result = true;
        foreach ($keys as $key) {
            $result = $result && $this->delete($key);
        }

        return $result;
    }

    public function has(string $key): bool
    {
        $key = $this->prefix . $key;

        return isset($_COOKIE[$key]);
    }
}
