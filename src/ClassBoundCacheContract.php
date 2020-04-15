<?php

declare(strict_types=1);

namespace TheCodingMachine\CacheUtils;

use ReflectionClass;

class ClassBoundCacheContract implements ClassBoundCacheContractInterface
{
    private ClassBoundCacheInterface $classBoundCache;

    public function __construct(ClassBoundCacheInterface $classBoundCache)
    {
        $this->classBoundCache = $classBoundCache;
    }

    /**
     * @param ReflectionClass<object> $reflectionClass
     * @param string $key An optional key to differentiate between cache items attached to the same class.
     *
     * @return mixed
     */
    public function get(ReflectionClass $reflectionClass, callable $resolver, string $key = '', ?int $ttl = null)
    {
        $cacheKey = $reflectionClass->getName() . '__' . $key;
        $item = $this->classBoundCache->get($cacheKey);
        if ($item !== null) {
            return $item;
        }

        $item = $resolver();

        $this->classBoundCache->set($cacheKey, $item, $reflectionClass, $ttl);

        return $item;
    }
}
