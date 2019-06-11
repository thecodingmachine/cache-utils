<?php

declare(strict_types=1);

namespace TheCodingMachine\CacheUtils;

/**
 * Cache items. Items expiration is bound to the modification time of a file linked to the item.
 */
interface FileBoundCacheInterface
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
     * @param array<int, string> $fileNames If one of these files is touched, the cache item is invalidated.
     */
    public function set(string $key, $item, array $fileNames, ?int $ttl = null): void;
}
