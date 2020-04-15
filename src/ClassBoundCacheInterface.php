<?php

declare(strict_types=1);

namespace TheCodingMachine\CacheUtils;

use ReflectionClass;

/**
 * Cache items. Items expiration is bound to the modification time of a PHP class.
 */
interface ClassBoundCacheInterface
{
    /**
     * Fetches an element from the cache by key.
     *
     * @return mixed
     */
    public function get(string $key);

    /**
     * Stores an item in the cache.
     *
     * @param mixed $item The item must be serializable.
     * @param ReflectionClass<object> $refClass If the class is modified, the cache item is invalidated.
     */
    public function set(string $key, $item, ReflectionClass $refClass, ?int $ttl = null): void;
}
