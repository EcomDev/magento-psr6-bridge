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

namespace EcomDev\MagentoPsr6Bridge;

/**
 * Interface provides a possibility
 * to extract cache value from cache item
 *
 * In default PSR-6 implementation, there are some setters
 * but no retrieval of value after it was set
 */
interface ExtractableCacheValueInterface
{
    /**
     * Returns cache value, that was set or loaded before
     *
     * @return mixed|null
     */
    public function getCacheValue();
}
