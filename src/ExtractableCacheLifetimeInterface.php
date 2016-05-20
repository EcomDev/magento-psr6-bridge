<?php

namespace EcomDev\MagentoPsr6Bridge;

/**
 * Interface provides a possibility
 * to extract lifetime from cache item
 *
 * In default PSR-6 implementation, there are some setters
 * but no retrieval of lifetime, so this one is vital for Magento implementation
 */
interface ExtractableCacheLifetimeInterface
{
    /**
     * Returns cache lifetime specified by public api user
     *
     * Time is calculated for both methods expiresAt() and expiresAfter()
     *
     * @return int|null
     */
    public function getCacheLifetime();
}
