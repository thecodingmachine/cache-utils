[![Latest Stable Version](https://poser.pugx.org/thecodingmachine/cache-utils/v/stable)](https://packagist.org/packages/thecodingmachine/cache-utils)
[![Total Downloads](https://poser.pugx.org/thecodingmachine/cache-utils/downloads)](https://packagist.org/packages/thecodingmachine/cache-utils)
[![Latest Unstable Version](https://poser.pugx.org/thecodingmachine/cache-utils/v/unstable)](https://packagist.org/packages/thecodingmachine/cache-utils)
[![License](https://poser.pugx.org/thecodingmachine/cache-utils/license)](https://packagist.org/packages/thecodingmachine/cache-utils)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/thecodingmachine/cache-utils/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/thecodingmachine/cache-utils/?branch=master)
[![Build Status](https://travis-ci.org/thecodingmachine/cache-utils.svg?branch=master)](https://travis-ci.org/thecodingmachine/cache-utils)
[![Coverage Status](https://coveralls.io/repos/thecodingmachine/cache-utils/badge.svg?branch=master&service=github)](https://coveralls.io/github/thecodingmachine/cache-utils?branch=master)

## Why?

This package contains a number of utility classes to play with PSR-16 caches.

### File bound cache

Most PHP cache systems (like PSR-6 or PSR-16) are storing items in cache and attributing to items a time to live (TTL).

If you are developing a PHP framework or a PHP analysis library that relies a lot on reflection, it is quite common 
to have cache items that are related to PHP files or PHP classes.

For instance, Doctrine Annotations in a class do not change unless the class file(s) is changed. Therefore, it makes
sense to bind the cache invalidation to the modification date of the file. *thecodingmachine/cache-utils* provides just that.

```php
namespace TheCodingMachine\CacheUtils\FileBoundCache;

$fileBoundCache = new FileBoundCache($psr16Cache);

// Put the $myDataToCache object in cache.
// If 'FooBar.php' and 'FooBaz.php' are modified, the cache item is purged.
$fileBoundCache->set('cache_key', $myDataToCache, 
[
    'FooBar.php',
    'FooBaz.php'
]);

// Fetching data
$myDataToCache = $fileBoundCache->get('cache_key');
```

You can also use the `MemoryAdapter` to store the cache in memory for even faster access in the same query.

```php
namespace TheCodingMachine\CacheUtils\FileBoundCache;
namespace TheCodingMachine\CacheUtils\MemoryAdapter;

$fileBoundCache = new MemoryAdapter(new FileBoundCache($psr16Cache));
```
