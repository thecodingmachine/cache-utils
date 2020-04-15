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
use TheCodingMachine\CacheUtils\Fixtures\B;
use TheCodingMachine\CacheUtils\Fixtures\C;
use TheCodingMachine\CacheUtils\Fixtures\D;
use function touch;

class ClassBoundCacheTest extends TestCase
{
    /**
     * @dataProvider touchFile
     */
    public function testClassBoundCache($classToTouch)
    {
        $cache = new ArrayAdapter();
        $fileBoundCache = new FileBoundCache($cache, 'prefix');
        $classBoundCache = new ClassBoundCache($fileBoundCache, true, true, true);

        $classBoundCache->set('foo', 'bar', new ReflectionClass(A::class));

        $this->assertSame('bar', $classBoundCache->get('foo'));

        $classToTouch = new ReflectionClass($classToTouch);
        sleep(1);
        clearstatcache($classToTouch->getFileName());
        touch($classToTouch->getFileName());

        $this->assertNull($classBoundCache->get('foo'));
    }

    public function touchFile()
    {
        return [
            [ B::class ],
            [ C::class ],
            [ D::class ],
        ];
    }
}
