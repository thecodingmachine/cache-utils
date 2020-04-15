<?php

declare(strict_types=1);

namespace TheCodingMachine\CacheUtils;

use ReflectionClass;

interface ClassBoundCacheContractInterface
{
    /**
     * @param ReflectionClass<object> $reflectionClass
     * @param string $key An optional key to differentiate between cache items attached to the same class.
     *
     * @return mixed
     */
    public function get(ReflectionClass $reflectionClass, callable $resolver, string $key = '', ?int $ttl = null);
}
