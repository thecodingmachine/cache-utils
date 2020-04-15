<?php

namespace TheCodingMachine\CacheUtils;

use Symfony\Component\Cache\Adapter\ArrayAdapter;
use function clearstatcache;
use function file_get_contents;
use function file_put_contents;
use ReflectionClass;
use function sleep;
use function str_replace;
use Symfony\Component\Cache\Simple\ArrayCache;
use function sys_get_temp_dir;
use PHPUnit\Framework\TestCase;
use TheCodingMachine\CacheUtils\Fixtures\A;
use function touch;

class ClassBoundMemoryAdapterTest extends TestCase
{
    public function testMemory(): void
    {
        $cache = new ArrayAdapter();
        $fileBoundCache = new FileBoundCache($cache, 'prefix');
        $classBoundCache = new ClassBoundCache($fileBoundCache);
        $adapter = new ClassBoundMemoryAdapter($classBoundCache);

        $classToTouch = new ReflectionClass(A::class);
        sleep(1);
        clearstatcache($classToTouch->getFileName());
        touch($classToTouch->getFileName());

        $adapter->set('foo', 'bar', $classToTouch);

        $this->assertSame('bar', $adapter->get('foo'));

        $adapter2 = new ClassBoundMemoryAdapter($classBoundCache);
        $this->assertSame('bar', $adapter2->get('foo'));

        sleep(1);
        clearstatcache($classToTouch->getFileName());
        touch($classToTouch->getFileName());

        $this->assertSame('bar', $adapter->get('foo'));
    }
}
