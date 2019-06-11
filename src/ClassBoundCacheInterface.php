<?php


namespace TheCodingMachine\CacheUtils;

/**
 * Cache items. Items expiration is bound to the modification time of a PHP class.
 */
interface ClassBoundCacheInterface
{
    /**
     * Fetches an element from the cache by key.
     *
     * @param string $key
     * @return mixed
     */
    public function get(string $key);

    /**
     * Stores an item in the cache.
     *
     * @param string $key
     * @param mixed $item The item must be serializable.
     * @param string $className Fully qualified class name.
     */
    public function set(string $key, $item, string $className): void;
}
