<?php

declare(strict_types=1);

namespace TheCodingMachine\CacheUtils;

use ReflectionClass;
use function array_merge;

class ClassBoundCache implements ClassBoundCacheInterface
{
    /** @var FileBoundCacheInterface */
    private $fileBoundCache;
    /** @var bool */
    private $analyzeParentClasses;
    /** @var bool */
    private $analyzeTraits;
    /** @var bool */
    private $analyzeInterfaces;

    public function __construct(FileBoundCacheInterface $fileBoundCache, bool $analyzeParentClasses = true, bool $analyzeTraits = true, bool $analyzeInterfaces = false)
    {
        $this->fileBoundCache = $fileBoundCache;
        $this->analyzeParentClasses = $analyzeParentClasses;
        $this->analyzeTraits = $analyzeTraits;
        $this->analyzeInterfaces = $analyzeInterfaces;
    }

    /**
     * Fetches an element from the cache by key.
     *
     * @return mixed
     */
    public function get(string $key)
    {
        return $this->fileBoundCache->get($key);
    }

    /**
     * Stores an item in the cache.
     *
     * @param mixed $item The item must be serializable.
     * @param ReflectionClass<object> $refClass If the class is modified, the cache item is invalidated.
     */
    public function set(string $key, $item, ReflectionClass $refClass, ?int $ttl = null): void
    {
        $files = $this->getFilesForClass($refClass);

        $this->fileBoundCache->set($key, $item, $files, $ttl);
    }

    /**
     * @param ReflectionClass<object> $refClass
     *
     * @return array<int, string>
     */
    private function getFilesForClass(ReflectionClass $refClass): array
    {
        $files = [];
        $file = $refClass->getFileName();
        if ($file !== false) {
            $files[] = $file;
        }

        if ($this->analyzeParentClasses && $refClass->getParentClass() !== false) {
            $files = array_merge($files, $this->getFilesForClass($refClass->getParentClass()));
        }

        if ($this->analyzeTraits) {
            foreach ($refClass->getTraits() as $trait) {
                $files = array_merge($files, $this->getFilesForClass($trait));
            }
        }

        if ($this->analyzeInterfaces) {
            foreach ($refClass->getInterfaces() as $interface) {
                $files = array_merge($files, $this->getFilesForClass($interface));
            }
        }

        return $files;
    }
}
