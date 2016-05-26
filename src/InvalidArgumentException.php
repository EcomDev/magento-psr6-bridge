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
 * @copyright  Copyright (c) 2016 EcomDev BV (http://www.ecomdev.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author     Ivan Chepurnyi <ivan@ecomdev.org>
 */

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
