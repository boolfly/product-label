<?php
/************************************************************
 * *
 *  * Copyright © Boolfly. All rights reserved.
 *  * See COPYING.txt for license details.
 *  *
 *  * @author    info@boolfly.com
 * *  @project   Product Label
 */
namespace Boolfly\ProductLabel\Observer;

use Boolfly\ProductLabel\Helper\Rule;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class AfterLoadProductCollection
 *
 * @package Boolfly\ProductLabel\Observer
 */
class AfterLoadProductCollection implements ObserverInterface
{
    /**
     * @var Rule
     */
    private $helperRule;

    /**
     * AfterLoadProductCollection constructor.
     *
     * @param Rule $helperRule
     */
    public function __construct(
        Rule $helperRule
    ) {
        $this->helperRule = $helperRule;
    }

    /**
     * @param Observer $observer
     * @throws \Exception
     */
    public function execute(Observer $observer)
    {
        /** @var Collection $collection */
        $collection = $observer->getEvent()->getData('collection');
        $this->helperRule->addRuleIdsToCollection($collection);
    }
}
