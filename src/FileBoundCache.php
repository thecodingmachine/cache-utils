<?php


namespace TheCodingMachine\CacheUtils;


use function filemtime;
use Psr\SimpleCache\CacheInterface;
use function str_replace;
use TheCodingMachine\CacheUtils\FileAccessException;

class FileBoundCache implements FileBoundCacheInterface
{
    /**
     * @var CacheInterface
     */
    private $cache;
    /**
     * @var string
     */
    private $cachePrefix;

    /**
     * @param CacheInterface $cache The underlying cache system.
     * @param string $cachePrefix The prefix to add to the cache.
     */
    public function __construct(CacheInterface $cache, string $cachePrefix = '')
    {
        $this->cache = $cache;
        $this->cachePrefix = str_replace(['\\', '{', '}', '(', ')', '/', '@', ':'], '_', $cachePrefix);
    }

    /**
     * Fetches an element from the cache by key.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function get(string $key, $default = null)
    {
        $item = $this->cache->get($this->cachePrefix.str_replace(['\\', '{', '}', '(', ')', '/', '@', ':'], '_', $key));
        if ($item !== null) {
            [
                'files' => $files,
                'data' => $data,
            ] = $item;

            $expired = false;
            foreach ($files as $fileName => $fileMTime) {
                if ($fileMTime !== @filemtime($fileName)) {
                    $expired = true;
                    break;
                }
            }

            if (!$expired) {
                return $data;
            }
        }

        return $default;
    }

    /**
     * Stores an item in the cache.
     *
     * @param string $key
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

        $this->cache->set($this->cachePrefix.str_replace(['\\', '{', '}', '(', ')', '/', '@', ':'], '_', $key), [
            'files' => $files,
            'data' => $item,
        ], $ttl);
    }
}
