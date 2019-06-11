<?php

namespace TheCodingMachine\CacheUtils;

use function clearstatcache;
use function file_get_contents;
use function file_put_contents;
use function str_replace;
use Symfony\Component\Cache\Simple\ArrayCache;
use function sys_get_temp_dir;
use PHPUnit\Framework\TestCase;
use function touch;

class FileBoundCacheTest extends TestCase
{
    public function testFileBoundCache()
    {
        $cache = new ArrayCache();
        $fileBoundCache = new FileBoundCache($cache, 'prefix');

        $tmpPath = sys_get_temp_dir().'/tmpCacheTest';
        touch($tmpPath);

        $fileBoundCache->set('foo', 'bar', [
            $tmpPath
        ]);

        $this->assertSame('bar', $fileBoundCache->get('foo'));

        sleep(1);
        clearstatcache($tmpPath);
        touch($tmpPath);

        $this->assertNull($fileBoundCache->get('foo'));
    }

    public function testException()
    {
        $cache = new ArrayCache();
        $fileBoundCache = new FileBoundCache($cache, 'prefix');

        $this->expectException(FileAccessException::class);
        $fileBoundCache->set('foo', 'bar', [
            'notExists'
        ]);
    }
}
