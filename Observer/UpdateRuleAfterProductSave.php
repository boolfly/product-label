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

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Boolfly\ProductLabel\Helper\Rule;
use Magento\Framework\Message\ManagerInterface;
use Boolfly\ProductLabel\Model\Indexer\Row;
use Magento\Catalog\Model\Product;

/**
 * Class UpdateRuleAfterProductSave
 *
 * @package Boolfly\ProductLabel\Observer
 */
class UpdateRuleAfterProductSave implements ObserverInterface
{
    /**
     * @var Rule
     */
    private $indexerRow;

    /**
     * @var ManagerInterface
     */
    private $messageManager;

    /**
     * UpdateRuleAfterProductSave constructor.
     *
     * @param Row              $indexerRow
     * @param ManagerInterface $messageManager
     */
    public function __construct(
        Row $indexerRow,
        ManagerInterface $messageManager
    ) {
        $this->indexerRow     = $indexerRow;
        $this->messageManager = $messageManager;
    }

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        try {
            $product = $observer->getEvent()->getData('product');
            if ($product instanceof Product && $product->getId()) {
                $this->indexerRow->execute($product->getId());
            }
        } catch (\Exception $e) {
            $this->messageManager->addWarningMessage('Something went wrong while updating product label.');
        }
    }
}
