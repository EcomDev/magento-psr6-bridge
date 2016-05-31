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


namespace spec\EcomDev\MagentoPsr6Bridge\Model;

use Magento\Framework\App\Cache\Type\FrontendPool;
use Magento\Framework\Cache\Frontend\Decorator\TagScope;
use Magento\Framework\Cache\FrontendInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class CacheTypeSpec extends ObjectBehavior
{
    public function let(FrontendPool $pool)
    {
        $this->beConstructedWith($pool);
    }

    function it_implements_frontend_cache_interface()
    {
        $this->shouldImplement(FrontendInterface::class);
    }

    function it_extends_tag_scope_interface()
    {
        $this->shouldHaveType(TagScope::class);
    }

    function it_creates_frontend_instance_via_pool_on_first_interaction(
        FrontendPool $pool,
        FrontendInterface $cacheFrontend
    )
    {
        $pool->get('psr6')->willReturn($cacheFrontend)->shouldBeCalled();

        $cacheFrontend->load('test_key1')
            ->willReturn('123123');

        $this->load('test_key1')->shouldReturn('123123');
    }

    function it_always_returns_psr6_cache_tag_for_tag_scope()
    {
        $this->getTag()->shouldReturn('PSR6');
    }
}
