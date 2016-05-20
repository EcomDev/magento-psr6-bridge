<?php

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
