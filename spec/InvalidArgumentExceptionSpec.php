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

class InvalidArgumentExceptionSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('Some dummy identifier');
    }

    function it_should_implement_psr_invalid_argument_interface_and_base_invalid_argument_class()
    {
        $this->shouldImplement(Cache\InvalidArgumentException::class);
        $this->shouldImplement(\InvalidArgumentException::class);
    }

    function it_should_return_message_passed_in_constructor()
    {
        $this->beConstructedWith('some_identifier');
        $this->getMessage()->shouldReturn('Cache key "some_identifier" does not follow rules defined by PSR-6 standard');
    }
}
