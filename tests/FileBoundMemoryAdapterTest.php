<?php

namespace TheCodingMachine\CacheUtils;

use Symfony\Component\Cache\Adapter\ArrayAdapter;
use function clearstatcache;
use function file_get_contents;
use function file_put_contents;
use function sleep;
use function str_replace;
use Symfony\Component\Cache\Simple\ArrayCache;
use function sys_get_temp_dir;
use PHPUnit\Framework\TestCase;
use function touch;

class FileBoundMemoryAdapterTest extends TestCase
{
    public function testMemory(): void
    {
        $cache = new ArrayAdapter();
        $fileBoundCache = new FileBoundCache($cache, 'prefix');
        $adapter = new FileBoundMemoryAdapter($fileBoundCache);

        $tmpPath = sys_get_temp_dir().'/tmpCacheTest';
        touch($tmpPath);

        $adapter->set('foo', 'bar', [
            $tmpPath
        ]);

        $this->assertSame('bar', $adapter->get('foo'));

        $adapter2 = new FileBoundMemoryAdapter($fileBoundCache);
        $this->assertSame('bar', $adapter2->get('foo'));

        sleep(1);
        clearstatcache($tmpPath);
        touch($tmpPath);

        $this->assertSame('bar', $adapter->get('foo'));
    }
}
