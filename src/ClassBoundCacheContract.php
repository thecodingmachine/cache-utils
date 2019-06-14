<?php


namespace TheCodingMachine\CacheUtils;

use ReflectionClass;

class ClassBoundCacheContract implements ClassBoundCacheContractInterface
{
    /**
     * @var ClassBoundCacheInterface
     */
    private $classBoundCache;

    public function __construct(ClassBoundCacheInterface $classBoundCache)
    {
        $this->classBoundCache = $classBoundCache;
    }

    /**
     * @param ReflectionClass $reflectionClass
     * @param callable $resolver
     * @param string|null $key An optional key to differentiate between cache items attached to the same class.
     * @return mixed
     */
    public function get(ReflectionClass $reflectionClass, callable $resolver, string $key = '')
    {
        $cacheKey = $reflectionClass->getName().'__'.$key;
        $item = $this->classBoundCache->get($cacheKey);
        if ($item !== null) {
            return $item;
        }

        $item = $resolver();

        $this->classBoundCache->set($cacheKey, $item, $reflectionClass);

        return $item;
    }
}
