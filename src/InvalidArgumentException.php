<?php

namespace EcomDev\MagentoPsr6Bridge;

/**
 * Invalid argument exception implementation
 *
 * Generates error message based on passed cache key
 */
class InvalidArgumentException extends \InvalidArgumentException implements \Psr\Cache\InvalidArgumentException
{
    /**
     * Creates exception message based on cache key
     *
     * @param string $cacheKey
     */
    public function __construct($cacheKey)
    {
        parent::__construct(
            sprintf('Cache key "%s" does not follow rules defined by PSR-6 standard', $cacheKey)
        );
    }
}
