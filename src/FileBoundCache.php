<?php

declare(strict_types=1);

namespace TheCodingMachine\CacheUtils;

use Psr\Cache\CacheItemPoolInterface;
use Psr\SimpleCache\InvalidArgumentException;
use function filemtime;
use function str_replace;

class FileBoundCache implements FileBoundCacheInterface
{
    private CacheItemPoolInterface $cache;
    private string $cachePrefix;

    /**
     * @param CacheItemPoolInterface $cache The underlying PSR-6 cache system.
     * @param string $cachePrefix The prefix to add to the cache.
     */
    public function __construct(CacheItemPoolInterface $cache, string $cachePrefix = '')
    {
        $this->cache = $cache;
        $this->cachePrefix = str_replace(['\\', '{', '}', '(', ')', '/', '@', ':'], '_', $cachePrefix);
    }

    /**
     * Fetches an element from the cache by key.
     *
     * @param mixed $default
     *
     * @return mixed
     *
     * @throws InvalidArgumentException
     */
    public function get(string $key, $default = null)
    {
        $item = $this->cache->getItem($this->cachePrefix . str_replace(['\\', '{', '}', '(', ')', '/', '@', ':'], '_', $key));
        if ($item->isHit()) {
            [
                'files' => $files,
                'data' => $data,
            ] = $item->get();

            $expired = false;
            foreach ($files as $fileName => $fileMTime) {
                if ($fileMTime !== @filemtime($fileName)) {
                    $expired = true;
                    break;
                }
            }

            if (! $expired) {
                return $data;
            }
        }

        return $default;
    }

    /**
     * Stores an item in the cache.
     *
     * @param mixed $item The item must be serializable.
     * @param array<int, string> $fileNames If one of these files is touched, the cache item is invalidated.
     */
    public function set(string $key, $item, array $fileNames, ?int $ttl = null): void
    {
        $files = [];
        foreach ($fileNames as $fileName) {
            $fileMTime = @filemtime($fileName);
            if ($fileMTime === false) {
                throw FileAccessException::cannotAccessFileModificationTime($fileName);
            }

            $files[$fileName] = $fileMTime;
        }

        $cacheItem = $this->cache->getItem($this->cachePrefix . str_replace(['\\', '{', '}', '(', ')', '/', '@', ':'], '_', $key));
        $cacheItem->set([
            'files' => $files,
            'data' => $item,
        ]);
        $cacheItem->expiresAfter($ttl);
        $this->cache->save($cacheItem);
    }
}
