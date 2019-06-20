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
use TheCodingMachine\CacheUtils\FileBoundCache;

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

You can also use the `FileBoundMemoryAdapter` to store the cache in memory for even faster access in the same query.

```php
use TheCodingMachine\CacheUtils\FileBoundCache;
use TheCodingMachine\CacheUtils\FileBoundMemoryAdapter;

$fileBoundCache = new FileBoundMemoryAdapter(new FileBoundCache($psr16Cache));
```

### Class bound cache

You can also bind a cache item to a class / trait / interface using the `ClassBoundCache` class.
The cache will expire if the class / trait / interface is modified.

```php
use TheCodingMachine\CacheUtils\FileBoundCache;
use TheCodingMachine\CacheUtils\ClassBoundCache;

$fileBoundCache = new FileBoundCache($psr16Cache);
$classBoundCache = new ClassBoundCache($fileBoundCache);

// Put the $myDataToCache object in cache.
// If the FooBar class is modified, the cache item is purged.
$classBoundCache->set('cache_key', $myDataToCache, new ReflectionClass(FooBar::class));

// Fetching data
$myDataToCache = $classBoundCache->get('cache_key');
```

The `ClassBoundCache` constructor accepts 3 additional parameters:

```php

class ClassBoundCache implements ClassBoundCacheInterface
{
    public function __construct(FileBoundCacheInterface $fileBoundCache, bool $analyzeParentClasses = true, bool $analyzeTraits = true, bool $analyzeInterfaces = false)
}
```

- `$analyzeParentClasses`: if set to true, the cache will be invalidated if one of the parent classes is modified
- `$analyzeTraits`: if set to true, the cache will be invalidated if one of the traits is modified
- `$analyzeInterfaces`: if set to true, the cache will be invalidated if one of the interfaces implemented is modified

You can also use the `ClassBoundMemoryAdapter` to store the cache in memory for even faster access in the same query.

```php
use TheCodingMachine\CacheUtils\ClassBoundCache;
use TheCodingMachine\CacheUtils\ClassBoundMemoryAdapter;

$classBoundCache = new ClassBoundMemoryAdapter(new ClassBoundCache($psr16Cache));
```

### Easier interface with cache contracts

You can even get an easier to use class bound cache using the `ClassBoundCacheContract`.

```php
use TheCodingMachine\CacheUtils\FileBoundCache;
use TheCodingMachine\CacheUtils\ClassBoundCache;
use TheCodingMachine\CacheUtils\ClassBoundMemoryAdapter;
use TheCodingMachine\CacheUtils\ClassBoundCacheContract;

$fileBoundCache = new FileBoundCache($psr16Cache);
$classBoundCache = new ClassBoundMemoryAdapter(new ClassBoundCache($psr16Cache));
$classBoundCacheContract = new ClassBoundCacheContract(new ClassBoundCache($fileBoundCache));

// If the FooBar class is modified, the cache item is purged.
$item = $classBoundCache->get(new ReflectionClass(FooBar::class), function() {
    // ...
    // let's return the item to be cached.
    // this function is called only if the item is not in cache yet.
    return $item;
});
```

With cache contracts, there is not setters. Only a getter that takes in parameter a callable that will resolve the 
cache item if the item is not available in the cache.
