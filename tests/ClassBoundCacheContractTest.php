<?php

namespace TheCodingMachine\CacheUtils;

use Symfony\Component\Cache\Adapter\ArrayAdapter;
use function clearstatcache;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use function sleep;
use Symfony\Component\Cache\Simple\ArrayCache;
use TheCodingMachine\CacheUtils\Fixtures\A;
use function touch;

class ClassBoundCacheContractTest extends TestCase
{
    public function testClassBoundCacheContract()
    {
        $cache = new ArrayAdapter();
        $fileBoundCache = new FileBoundCache($cache, 'prefix');
        $classBoundCache = new ClassBoundCache($fileBoundCache, true, true, true);
        $classBoundCacheContract = new ClassBoundCacheContract($classBoundCache);

        $val = 0;

        $newVal = $classBoundCacheContract->get(new ReflectionClass(A::class), function() use (&$val) {
            return ++$val;
        });

        $this->assertSame(1, $newVal);

        $newVal2 = $classBoundCacheContract->get(new ReflectionClass(A::class), function() use (&$val) {
            return ++$val;
        });

        $this->assertSame(1, $newVal2);

        $classToTouch = new ReflectionClass(A::class);
        sleep(1);
        clearstatcache($classToTouch->getFileName());
        touch($classToTouch->getFileName());

        $newVal3 = $classBoundCacheContract->get(new ReflectionClass(A::class), function() use (&$val) {
            return ++$val;
        });

        $this->assertSame(2, $newVal3);
    }

}
