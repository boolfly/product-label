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

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Boolfly\ProductLabel\Helper\Rule;

/**
 * Class AddLabelRuleData
 *
 * @package Boolfly\ProductLabel\Observer
 */
class AddLabelRuleData implements ObserverInterface
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
        /** @var Product $product */
        $product = $observer->getEvent()->getData('product');
        if ($product instanceof Product) {
            $this->helperRule->addRuleIds($product);
        }
    }
}
