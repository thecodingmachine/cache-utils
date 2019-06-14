<?php

namespace TheCodingMachine\CacheUtils;

use ReflectionClass;

interface ClassBoundCacheContractInterface
{
    /**
     * @param ReflectionClass $reflectionClass
     * @param callable $resolver
     * @param string|null $key An optional key to differentiate between cache items attached to the same class.
     * @return mixed
     */
    public function get(ReflectionClass $reflectionClass, callable $resolver, string $key = '');
}
