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

use Magento\Framework\App\Cache\Type\FrontendPool;
use Magento\Framework\Cache\Frontend\Decorator\TagScope;
use Magento\Framework\Cache\FrontendInterface;

/**
 * Cache type instance
 *
 * Used in cache pool as frontend interface instance
 */
class CacheType extends TagScope
{
    /**
     * Cache type identifier
     *
     * @var string
     */
    const CACHE_TYPE = 'psr6';

    /**
     * Cache tag
     *
     * @var string
     */
    const CACHE_TAG = 'PSR6';

    /**
     * Frontend pool
     *
     * @var FrontendPool
     */
    private $frontendPool;

    /**
     * Uses frontend pool to later on
     * instantiate a needed instance
     *
     * @param FrontendPool $frontendPool
     */
    public function __construct(FrontendPool $frontendPool)
    {
        $this->frontendPool = $frontendPool;
    }

    // I had to put ignore in order to pass PSR2 check when override core method :(
    // @codingStandardsIgnoreStart
    /**
     * Returns a frontend on a first call to frontend interface methods
     * 
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     *
     * @return FrontendInterface
     */
    protected function _getFrontend()
    {
        // @codingStandardsIgnoreEnd
        $frontend = parent::_getFrontend();

        if ($frontend === null) {
            $frontend = $this->frontendPool->get(self::CACHE_TYPE);
        }

        return $frontend;
    }

    /**
     * Returns a cache tag
     *
     * @return string
     */
    public function getTag()
    {
        return self::CACHE_TAG;
    }
}
