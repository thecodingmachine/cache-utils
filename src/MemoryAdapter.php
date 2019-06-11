<?php

declare(strict_types=1);

namespace TheCodingMachine\CacheUtils;

/**
 * An adapter around a FileBoundCacheInterface that stores values in memory for the current request (for maximum performance)
 */
class MemoryAdapter implements FileBoundCacheInterface
{
    /** @var array<string, mixed> */
    private $items;
    /** @var FileBoundCacheInterface */
    private $fileBoundCache;

    public function __construct(FileBoundCacheInterface $fileBoundCache)
    {
        $this->fileBoundCache = $fileBoundCache;
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

        return $this->items[$key] = $this->fileBoundCache->get($key);
    }

    /**
     * Stores an item in the cache.
     *
     * @param mixed $item The item must be serializable.
     * @param array<int, string> $fileNames If one of these files is touched, the cache item is invalidated.
     */
    public function set(string $key, $item, array $fileNames, ?int $ttl = null): void
    {
        $this->items[$key] = $item;
        $this->fileBoundCache->set($key, $item, $fileNames, $ttl);
    }
}
