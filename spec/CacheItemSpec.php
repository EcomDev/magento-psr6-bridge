<?php

namespace spec\EcomDev\MagentoPsr6Bridge;

use Psr\Cache\CacheItemInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class CacheItemSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('dummy_key', true, 'dummy_value');
    }

    function it_should_implement_cache_item_interface()
    {
        $this->shouldImplement(CacheItemInterface::class);
    }

    function it_returns_key_that_was_passed_in_constructor()
    {
        $this->getKey()->shouldReturn('dummy_key');
    }

    function it_returns_value_that_was_passed_in_constructor()
    {
        $this->get()->shouldReturn('dummy_value');
    }

    function it_returns_hit_only_if_it_was_specified_in_constructor()
    {
        $this->isHit()->shouldReturn(true);
    }

    function it_returns_null_as_value_even_if_it_is_not_a_hit_and_value_has_been_set_before()
    {
        $this->beConstructedWith('key1', false, 'value!');
        $this->get()->shouldReturn(null);
    }

    function it_does_not_require_value_to_be_set_and_deafaults_to_null()
    {
        $this->beConstructedWith('key1', true);
        $this->get()->shouldReturn(null);
    }

    function it_is_possible_to_set_cache_value()
    {
        $this->set('some_cache_value')->shouldReturn($this);
        $this->get()->shouldReturn('some_cache_value');
    }

    function it_does_not_have_any_cache_lifetime_set_by_default()
    {
        $this->getCacheLifetime()->shouldReturn(null);
    }

    function it_calculates_difference_between_expires_at_data_and_current_time_to_return_correct_lifetime()
    {
        $inTwoDays = new \DateTime();
        $inTwoDays->add(\DateInterval::createFromDateString('2 day'));
        $inTwoDays->setTimezone(new \DateTimeZone('America/Los_Angeles'));

        $this->expiresAt($inTwoDays)->shouldReturn($this);
        $this->getCacheLifetime()->shouldReturn(172800);
    }

    function it_is_possible_to_expire_cache_entry_with_date_interval()
    {
        $this->expiresAfter(\DateInterval::createFromDateString('1 hour'))->shouldReturn($this);
        $this->getCacheLifetime()->shouldReturn(3600);
    }

    function it_is_possible_to_expire_cache_entry_with_int_interval()
    {
        $this->expiresAfter(360)->shouldReturn($this);
        $this->getCacheLifetime()->shouldReturn(360);
    }

    function it_is_possible_to_specify_null_as_expiration_time_of_cache()
    {
        $this->expiresAfter(360)->shouldReturn($this);
        $this->expiresAfter(null)->shouldReturn($this);
        $this->getCacheLifetime()->shouldReturn(null);
    }
}
