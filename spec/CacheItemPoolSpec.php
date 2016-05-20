<?php

namespace spec\EcomDev\MagentoPsr6Bridge;

use Psr\Cache\CacheItemPoolInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class CacheItemPoolSpec extends ObjectBehavior
{
    function it_should_implement_cache_item_pool_interface()
    {
        $this->shouldHaveType(CacheItemPoolInterface::class);
    }
}
