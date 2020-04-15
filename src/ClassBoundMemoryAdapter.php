<?php

declare(strict_types=1);

namespace TheCodingMachine\CacheUtils;

use ReflectionClass;

/**
 * An adapter around a FileBoundCacheInterface that stores values in memory for the current request (for maximum performance)
 */
class ClassBoundMemoryAdapter implements ClassBoundCacheInterface
{
    /** @var array<string, mixed> */
    private $items;
    /** @var ClassBoundCacheInterface */
    private $classBoundCache;

    public function __construct(ClassBoundCacheInterface $classBoundCache)
    {
        $this->classBoundCache = $classBoundCache;
    }

    /**
     * Fetches an element from the cache by key.
     *
     * @return mixed
     */
    public function get(string $key)
    {
        if (isset($this->items[$key])) {
            return $this->items[$key];
        }

        return $this->items[$key] = $this->classBoundCache->get($key);
    }

    /**
     * Stores an item in the cache.
     *
     * @param mixed $item The item must be serializable.
     * @param ReflectionClass<object> $refClass If the class is modified, the cache item is invalidated.
     */
    public function set(string $key, $item, ReflectionClass $refClass, ?int $ttl = null): void
    {
        $this->items[$key] = $item;
        $this->classBoundCache->set($key, $item, $refClass, $ttl);
    }
}
