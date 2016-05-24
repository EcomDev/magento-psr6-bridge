<?php

namespace spec\EcomDev\MagentoPsr6Bridge;

use Magento\Framework\Cache\FrontendInterface;
use Magento\Framework\ObjectManager\FactoryInterface;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use EcomDev\MagentoPsr6Bridge\CacheItemFactory;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class CacheItemPoolSpec extends ObjectBehavior
{
    /**
     * Cache item factory
     *
     * @var CacheItemFactory
     */
    private $cacheItemFactory;
    /**
     * Cache frontend
     *
     * @var FrontendInterface
     */
    private $cacheFrontend;

    function let(CacheItemFactory $cacheItemFactory, FrontendInterface $cacheFrontend)
    {
        $this->cacheItemFactory = $cacheItemFactory;
        $this->cacheFrontend = $cacheFrontend;
        $this->beConstructedWith($this->cacheItemFactory, $this->cacheFrontend, 'prefix_', ['tag1', 'tag2']);
    }

    function it_should_implement_cache_item_pool_interface()
    {
        $this->shouldHaveType(CacheItemPoolInterface::class);
    }

    function it_returns_cache_item_if_there_is_a_hit(CacheItemInterface $cacheItem)
    {
        $this->cacheFrontend->load('prefix_key1')
            ->willReturn(serialize('test'))
            ->shouldBeCalled();

        $this->cacheItemFactory
            ->create(['key' => 'key1', 'isHit' => true, 'value' => 'test'])
            ->willReturn($cacheItem)
            ->shouldBeCalled();

        $this->getItem('key1')->shouldReturn($cacheItem);
    }

    function it_returns_cache_item_if_there_is_no_hit(CacheItemInterface $cacheItem)
    {
        $this->cacheFrontend->load('prefix_key1')
            ->willReturn(false)
            ->shouldBeCalled();

        $this->cacheItemFactory
            ->create(['key' => 'key1', 'isHit' => false])
            ->willReturn($cacheItem)
            ->shouldBeCalled();

        $this->getItem('key1')->shouldReturn($cacheItem);
    }

    function it_returns_multiple_cache_items(CacheItemInterface $cacheItemOne, CacheItemInterface $cacheItemTwo)
    {
        $this->cacheFrontend->load('prefix_key1')
            ->willReturn(false);

        $this->cacheFrontend->load('prefix_key2')
            ->willReturn(false);

        $this->cacheItemFactory
            ->create(['key' => 'key1', 'isHit' => false])
            ->willReturn($cacheItemOne);

        $this->cacheItemFactory
            ->create(['key' => 'key2', 'isHit' => false])
            ->willReturn($cacheItemTwo);

        $this->getItems(['key1', 'key2'])->shouldReturn([
            'key1' => $cacheItemOne,
            'key2' => $cacheItemTwo
        ]);
    }

    function it_checks_if_item_exists_in_cache_without_creating_cache_item()
    {
        $this->cacheFrontend->load('prefix_key1')
            ->willReturn(serialize('test'));

        $this->cacheFrontend->load('prefix_key2')
            ->willReturn(false);

        $this->cacheItemFactory->create(Argument::any())
            ->shouldNotBeCalled();

        $this->hasItem('key1')->shouldReturn(true);
        $this->hasItem('key2')->shouldReturn(false);

    }

    function it_clears_magento_cache_by_tags()
    {
        $this->cacheFrontend
            ->clean(\Zend_Cache::CLEANING_MODE_MATCHING_TAG, ['tag1', 'tag2'])
            ->shouldBeCalled()
            ->willReturn(true);

        $this->clear()->shouldReturn(true);
    }

    function it_does_not_clear_cache_if_no_tags_specified_to_prevent_full_cache_flush()
    {
        $this->cacheFrontend
            ->clean(Argument::any(), Argument::any())
            ->shouldNotBeCalled();

        $this->beConstructedWith(
            $this->cacheItemFactory,
            $this->cacheFrontend,
            'prefix_',
            []
        );

        $this->clear()->shouldReturn(false);
    }
}
