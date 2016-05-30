<?php
/**
 * Magento PSR-6 Bridge
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 *
 * @copyright Copyright (c) 2016 EcomDev BV (http://www.ecomdev.org)
 * @license   https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author    Ivan Chepurnyi <ivan@ecomdev.org>
 */

namespace EcomDev\MagentoPsr6Bridge\Model;

use Magento\Framework\Cache\FrontendInterface;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use EcomDev\MagentoPsr6Bridge\Model\CacheItemFactory;
use EcomDev\MagentoPsr6Bridge\ExtractableCacheLifetimeInterface;
use EcomDev\MagentoPsr6Bridge\InvalidArgumentException;

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
        return $this->cacheFrontend->remove(
            $this->prepareKey($key)
        );
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
        foreach ($keys as $key) {
            if (!$this->deleteItem($key)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Persists a cache item immediately.
     *
     * @param CacheItemInterface $item
     *   The cache item to save.
     *
     * @return bool
     *   True if the item was successfully persisted. False if there was an error.
     *
     * @throws InvalidArgumentException
     */
    public function save(CacheItemInterface $item)
    {
        $expirationTime = null;

        if ($item instanceof ExtractableCacheLifetimeInterface) {
            $expirationTime = $item->getCacheLifetime();
        }

        return $this->cacheFrontend->save(
            serialize($item->get()),
            $this->prepareKey($item->getKey()),
            $this->tags,
            $expirationTime
        );
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
        $this->defferedItems[] = $item;
        return true;
    }

    /**
     * Persists any deferred cache items.
     *
     * @return bool
     *   True if all not-yet-saved items were successfully saved or there were none. False otherwise.
     */
    public function commit()
    {
        foreach ($this->defferedItems as $item) {
            $this->save($item);
        }

        $this->defferedItems = [];
        return true;
    }

    /**
     * Prepares a key for cache storage
     *
     * @param string $key
     *
     * @return string
     *
     * @throws InvalidArgumentException
     */
    private function prepareKey($key)
    {
        if (!preg_match('/^[a-zA-Z0-9_-]+$/', $key)) {
            throw new InvalidArgumentException($key);
        }

        return $this->keyPrefix . strtr($key, '-', '_');
    }
}
