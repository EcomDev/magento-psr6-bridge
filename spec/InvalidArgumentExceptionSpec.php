<?php

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
