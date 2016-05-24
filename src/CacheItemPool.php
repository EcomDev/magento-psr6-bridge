<?php

namespace EcomDev\MagentoPsr6Bridge;

use Magento\Framework\Cache\FrontendInterface;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use EcomDev\MagentoPsr6Bridge\CacheItemFactory;

/**
 * Concrete cache item pool implementation
 */
class CacheItemPool implements CacheItemPoolInterface
{

    /**
     * Magento cache frontend interface
     *
     * @var FrontendInterface
     */
    private $cacheFrontend;

    /**
     * Factory for crating of cache item instances
     *
     * @var CacheItemFactory
     */
    private $cacheItemFactory;

    /**
     * List of cache tags to be assigned
     * to every cache entry saved with this pool
     *
     * @var string[]
     */
    private $tags;

    /**
     * Key prefix for your cache keys
     *
     * @var string
     */
    private $keyPrefix;

    /**
     * Items that are scheduled for deferred save process
     *
     * @var CacheItemInterface[]
     */
    private $defferedItems = [];

    /**
     * Configures Cache Item Pool Dependencies
     *
     * @param CacheItemFactory $cacheItemFactory
     * @param FrontendInterface $cacheFrontend
     * @param string $keyPrefix
     * @param \string[] $tags
     */
    public function __construct(
        CacheItemFactory $cacheItemFactory,
        FrontendInterface $cacheFrontend,
        $keyPrefix,
        array $tags = []
    ) {
    
        $this->cacheFrontend = $cacheFrontend;
        $this->cacheItemFactory = $cacheItemFactory;
        $this->tags = $tags;
        $this->keyPrefix = $keyPrefix;
    }


    /**
     * Returns a Cache Item representing the specified key.
     *
     * This method must always return a CacheItemInterface object, even in case of
     * a cache miss. It MUST NOT return null.
     *
     * @param string $key
     *   The key for which to return the corresponding Cache Item.
     *
     * @throws InvalidArgumentException
     *   If the $key string is not a legal value a \Psr\Cache\InvalidArgumentException
     *   MUST be thrown.
     *
     * @return CacheItemInterface
     *   The corresponding Cache Item.
     */
    public function getItem($key)
    {
        $cacheEntry = $this->cacheFrontend->load(
            $this->prepareKey($key)
        );

        if ($cacheEntry !== false) {
            $cacheEntry = unserialize($cacheEntry);
            return $this->cacheItemFactory->create(['key' => $key, 'isHit' => true, 'value' => $cacheEntry]);
        }

        return $this->cacheItemFactory->create(['key' => $key, 'isHit' => false]);
    }

    /**
     * Returns a traversable set of cache items.
     *
     * @param array $keys
     * An indexed array of keys of items to retrieve.
     *
     * @throws InvalidArgumentException
     *   If any of the keys in $keys are not a legal value a \Psr\Cache\InvalidArgumentException
     *   MUST be thrown.
     *
     * @return array|\Traversable
     *   A traversable collection of Cache Items keyed by the cache keys of
     *   each item. A Cache item will be returned for each key, even if that
     *   key is not found. However, if no keys are specified then an empty
     *   traversable MUST be returned instead.
     */
    public function getItems(array $keys = array())
    {
        $result = [];

        foreach ($keys as $key) {
            $result[$key] = $this->getItem($key);
        }

        return $result;
    }

    /**
     * Confirms if the cache contains specified cache item.
     *
     * Note: This method MAY avoid retrieving the cached value for performance reasons.
     * This could result in a race condition with CacheItemInterface::get(). To avoid
     * such situation use CacheItemInterface::isHit() instead.
     *
     * @param string $key
     *    The key for which to check existence.
     *
     * @throws InvalidArgumentException
     *   If the $key string is not a legal value a \Psr\Cache\InvalidArgumentException
     *   MUST be thrown.
     *
     * @return bool
     *  True if item exists in the cache, false otherwise.
     */
    public function hasItem($key)
    {
        if ($this->cacheFrontend->load($this->prepareKey($key)) !== false) {
            return true;
        }

        return false;
    }

    /**
     * Deletes all items in the pool.
     *
     * @return bool
     *   True if the pool was successfully cleared. False if there was an error.
     */
    public function clear()
    {
        if (empty($this->tags)) {
            return false;
        }

        return $this->cacheFrontend->clean(\Zend_Cache::CLEANING_MODE_MATCHING_TAG, $this->tags);
    }

    /**
     * Removes the item from the pool.
     *
     * @param string $key
     *   The key for which to delete
     *
     * @throws InvalidArgumentException
     *   If the $key string is not a legal value a \Psr\Cache\InvalidArgumentException
     *   MUST be thrown.
     *
     * @return bool
     *   True if the item was successfully removed. False if there was an error.
     */
    public function deleteItem($key)
    {
        // TODO Write spec, implement method
    }

    /**
     * Removes multiple items from the pool.
     *
     * @param array $keys
     *   An array of keys that should be removed from the pool.

     * @throws InvalidArgumentException
     *   If any of the keys in $keys are not a legal value a \Psr\Cache\InvalidArgumentException
     *   MUST be thrown.
     *
     * @return bool
     *   True if the items were successfully removed. False if there was an error.
     */
    public function deleteItems(array $keys)
    {
        // TODO Write spec, implement method
    }

    /**
     * Persists a cache item immediately.
     *
     * @param CacheItemInterface $item
     *   The cache item to save.
     *
     * @return bool
     *   True if the item was successfully persisted. False if there was an error.
     */
    public function save(CacheItemInterface $item)
    {
        // TODO Write spec, implement method
    }

    /**
     * Sets a cache item to be persisted later.
     *
     * @param CacheItemInterface $item
     *   The cache item to save.
     *
     * @return bool
     *   False if the item could not be queued or if a commit was attempted and failed. True otherwise.
     */
    public function saveDeferred(CacheItemInterface $item)
    {
        // TODO Write spec, implement method
    }

    /**
     * Persists any deferred cache items.
     *
     * @return bool
     *   True if all not-yet-saved items were successfully saved or there were none. False otherwise.
     */
    public function commit()
    {
        // TODO Write spec, implement method
    }

    /**
     * Prepares a key for cache storage
     *
     * @param string $key
     *
     * @return string
     */
    private function prepareKey($key)
    {
        return $this->keyPrefix . $key;
    }
}
