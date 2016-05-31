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

namespace spec\EcomDev\MagentoPsr6Bridge;

use Psr\Cache;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class CacheExceptionSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('Some dummy message', 0);
    }

    function it_should_implement_psr_cache_exception_interface_and_should_be_extended_from_runtime_exception()
    {
        $this->shouldImplement(Cache\CacheException::class);
        $this->shouldImplement(\RuntimeException::class);
    }

    function it_should_return_message_passed_in_constructor()
    {
        $this->getMessage()->shouldReturn('Some dummy message');
    }

    function it_should_return_code_that_was_passed_in_constructor()
    {
        $this->beConstructedWith('Another message', 9999);
        $this->getCode()->shouldReturn(9999);
    }

}
