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

use EcomDev\MagentoPsr6Bridge\Model\CacheItem;
use EcomDev\MagentoPsr6Bridge\InvalidArgumentException;
use Magento\Framework\Cache\FrontendInterface;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use EcomDev\MagentoPsr6Bridge\Model\CacheItemFactory;
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

    function it_allows_cache_key_with_dash(CacheItemInterface $cacheItem)
    {
        $this->cacheFrontend->load('prefix_key_1')
            ->willReturn(false)
            ->shouldBeCalled();

        $this->cacheItemFactory
            ->create(['key' => 'key-1', 'isHit' => false])
            ->willReturn($cacheItem)
            ->shouldBeCalled();

        $this->getItem('key-1')->shouldReturn($cacheItem);
    }

    function it_does_not_accept_not_compliant_cache_keys()
    {
        $this->shouldThrow(new InvalidArgumentException('$invalid#'))->duringGetItem('$invalid#');
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

    function it_removes_item_by_id()
    {
        $this->cacheFrontend->remove('prefix_key1')->willReturn(true)->shouldBeCalled();

        $this->deleteItem('key1')->shouldReturn(true);
    }

    function it_removes_multiple_items_by_ids_only_when_the_whole_sequence_work()
    {
        $this->cacheFrontend->remove('prefix_key1')->willReturn(true)->shouldBeCalled();
        $this->cacheFrontend->remove('prefix_key2')->willReturn(false)->shouldBeCalled();
        $this->cacheFrontend->remove('prefix_key3')->shouldNotBeCalled();

        $this->deleteItems(['key1', 'key2', 'key3'])->shouldReturn(false);
    }

    function it_removes_all_items_by_ids()
    {
        $this->cacheFrontend->remove('prefix_key1')->willReturn(true)->shouldBeCalled();
        $this->cacheFrontend->remove('prefix_key2')->willReturn(true)->shouldBeCalled();
        $this->cacheFrontend->remove('prefix_key3')->willReturn(true)->shouldBeCalled();

        $this->deleteItems(['key1', 'key2', 'key3'])->shouldReturn(true);
    }

    function it_saves_item_without_expiration_extraction_interface(CacheItemInterface $cacheItem)
    {
        $cacheItem->get()->willReturn([1, 2, 3])->shouldBeCalled();
        $cacheItem->getKey()->willReturn('key1')->shouldBeCalled();

        $this->cacheFrontend
            ->save(serialize([1, 2, 3]), 'prefix_key1', ['tag1', 'tag2'], null)
            ->willReturn(true)
            ->shouldBeCalled();

        $this->save($cacheItem)->shouldReturn(true);
    }

    function it_saves_item_with_expiration_time_and_value_provided_from_another_method(CacheItem $cacheItem)
    {
        $cacheItem->get()->shouldNotBeCalled();
        $cacheItem->getCacheValue()->willReturn([1, 2, 3])->shouldBeCalled();
        $cacheItem->getKey()->willReturn('key1')->shouldBeCalled();
        $cacheItem->getCacheLifetime()->willReturn(3600)->shouldBeCalled();

        $this->cacheFrontend
            ->save(serialize([1, 2, 3]), 'prefix_key1', ['tag1', 'tag2'], 3600)
            ->willReturn(true)
            ->shouldBeCalled();

        $this->save($cacheItem)->shouldReturn(true);
    }
    
    function it_returns_false_if_save_failes(CacheItemInterface $cacheItem)
    {
        $cacheItem->get()->willReturn([1, 2, 3])->shouldBeCalled();
        $cacheItem->getKey()->willReturn('key1')->shouldBeCalled();

        $this->cacheFrontend
            ->save(serialize([1, 2, 3]), 'prefix_key1', ['tag1', 'tag2'], null)
            ->willReturn(false)
            ->shouldBeCalled();

        $this->save($cacheItem)->shouldReturn(false);
    }

    function it_schedules_cache_item_save_but_not_saves(CacheItemInterface $cacheItem)
    {
        $cacheItem->get()->shouldNotBeCalled();
        $cacheItem->getKey()->shouldNotBeCalled();

        $this->saveDeferred($cacheItem)->shouldReturn(true);
    }

    function it_schedules_cache_item_save_and_saves_on_commit(
        CacheItemInterface $cacheItemOne,
        CacheItemInterface $cacheItemTwo
    )
    {
        $cacheItemOne->get()->willReturn([1, 2, 3])->shouldBeCalled();
        $cacheItemOne->getKey()->willReturn('key1')->shouldBeCalled();

        $cacheItemTwo->get()->willReturn([4, 5, 6])->shouldBeCalled();
        $cacheItemTwo->getKey()->willReturn('key2')->shouldBeCalled();

        $this->cacheFrontend
            ->save(serialize([1, 2, 3]), 'prefix_key1', ['tag1', 'tag2'], null)
            ->willReturn(true)
            ->shouldBeCalled();


        $this->cacheFrontend
            ->save(serialize([4, 5, 6]), 'prefix_key2', ['tag1', 'tag2'], null)
            ->willReturn(true)
            ->shouldBeCalled();

        $this->saveDeferred($cacheItemOne)->shouldReturn(true);
        $this->saveDeferred($cacheItemTwo)->shouldReturn(true);

        $this->commit()->shouldReturn(true);
    }

    function it_commits_deferred_cache_items_only_onces(
        CacheItemInterface $cacheItem
    )
    {
        $cacheItem->get()->willReturn([1, 2, 3])->shouldBeCalledTimes(1);
        $cacheItem->getKey()->willReturn('key1')->shouldBeCalledTimes(1);


        $this->cacheFrontend
            ->save(serialize([1, 2, 3]), 'prefix_key1', ['tag1', 'tag2'], null)
            ->willReturn(true)
            ->shouldBeCalledTimes(1);


        $this->saveDeferred($cacheItem)->shouldReturn(true);

        $this->commit()->shouldReturn(true);
        
        // This time nothing should happen
        $this->commit()->shouldReturn(true);
    }

}
