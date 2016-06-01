# Magento 2.0 PSR-6 Bridge [![Build Status](https://travis-ci.org/EcomDev/magento-psr6-bridge.svg)](https://travis-ci.org/EcomDev/magento-psr6-bridge)  [![Coverage Status](https://coveralls.io/repos/github/EcomDev/magento-psr6-bridge/badge.svg?branch=develop)](https://coveralls.io/github/EcomDev/magento-psr6-bridge?branch=develop)

This small module is a bridge, that allows you to integrate any PSR-6 compatible library into your Magento 2.0 project.

As well it is more convenient to use PSR-6 based cache pool than creating your custom cache type for a Magento module.

[Read more about PSR-6](http://www.php-fig.org/psr/psr-6/)

## Installation

1. Add module as dependency:
    
    ```bash
    composer require ecomdev/magento-psr6-bridge
    ```

2. Enable module

    ```bash
    bin/magento module:enable EcomDev_MagentoPsr6Bridge
    ```


3. Enable PSR-6 cache type

    ```bash
    bin/magento cache:enable psr6
    ```
    
## Usage

### Basic usage

If you already have a PSR-6 compatible library, that uses `Psr\Cache\CacheItemPoolInterface` in one of its components, it will work out of the box.
All the cache keys will be automatically prefixed with `psr6_` prefix in standard Magento cache storage. Also PSR6 cache tag will be applied automatically.   
 
### Using custom cache tags and prefix

If you would like to implement own cache invalidation on particular actions (ERP import, etc). 

Then you might find it convenient to use additional custom cache tags so items will be cleaned by them on invoking `clear()` method.

1. Add a new virtual type into your `di.xml` and use it for your instances

    ```xml
    <?xml version="1.0"?>
    <config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="urn:magento:framework:ObjectManager/etc/config.xsd">
        <virtualType name="yourModuleCustomCacheItemPoolInstance" type="EcomDev\MagentoPsr6Bridge\Model\CacheItemPool">
            <arguments>
                <argument name="tags" xsi:type="array">
                    <item name="custom_tag" xsi:type="string">CUSTOM_TAG</item>
                </argument>
                <argument name="keyPrefix" xsi:type="string">my_custom_prefix</argument>
            </arguments>
        </virtualType>
        <type name="Your\Module\Model\Item">
            <arguments>
                <argument name="cacheItemPool" xsi:type="object">yourModuleCustomCacheItemPoolInstance</argument>
            </arguments>
        </type>
    </config>
    ```

2. Now you can use it in your custom class

    ```php
    namespace Your\Module\Model;
    
    class Item
    {
        private $cacheItemPool;
        
        public function __construct(
            \Psr\Cache\CacheItemPoolInterface $cacheItemPool
        )
        {
            $this->cacheItemPool = $cacheItemPool;
        }
        
        public function doSomeStuff()
        {
            $item = $this->cacheItemPool->getItem('cache_key_id');
    
            if ($item->isHit()) {
                return sprintf('Cached: %s', $item->get());
            }
            
            $value = 'Value for cache';
    
            $item->set($value);
    
            $this->cacheItemPool->save($item);
            
            return sprintf('Not cached: %s', $value);
        }
        
        public function invalidate()
        {
            $this->cacheItemPool->clear();
            return $this;
        }
    
    }
    ```

### Use cache key generator

Modules comes out of the box with properly configured cache key generator, so if you do not want to take care about your cache key structure, but have PSR6 compatible, you can use it:

```php
namespace Your\Module\Model;
    
use EcomDev\CacheKey\InfoProviderInterface;

class Item implements InfoProviderInterface
{
    private $cacheItemPool;

    private $cacheKeyGenerator;

    public function __construct(
        \Psr\Cache\CacheItemPoolInterface $cacheItemPool,
        \EcomDev\CacheKey\GeneratorInterface $cacheKeyGenerator
    )
    {
        $this->cacheItemPool = $cacheItemPool;
        $this->cacheKeyGenerator = $cacheKeyGenerator;
    }

    public function getCacheKeyInfo()
    {
        return [
            'key' => 'value',
            'key1' => 'value1'
        ];
    }

    public function doSomeStuff()
    {
        $item = $this->cacheItemPool->getItem(
            $this->cacheKeyGenerator->generate($this)
        );

        if ($item->isHit()) {
            return sprintf('Cached: %s', $item->get());
        }

        $value = 'Value for cache';
        $item->set($value);

        $this->cacheItemPool->save($item);
        
        return sprintf('Not cached: %s', $value);
    }
}
```

This allows you remove all that terrible logic of cache key generation based on your class properties.

[Read more about cache key generator](https://github.com/ecomdev/cache-key)

## Contribution
Make a pull request based on develop branch
