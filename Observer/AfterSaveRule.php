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

use Boolfly\ProductLabel\Api\Data\RuleInterface;
use Boolfly\ProductLabel\Model\Indexer\Full;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class AfterSaveRule
 *
 * @package Boolfly\ProductLabel\Observer
 *
 * @event controller_boolfly_label_rule_save_entity_before
 */
class AfterSaveRule implements ObserverInterface
{
    /**
     * @var Full
     */
    private $indexerFull;

    /**
     * AfterSaveRule constructor.
     *
     * @param Full $indexerFull
     */
    public function __construct(
        Full $indexerFull
    ) {
        $this->indexerFull = $indexerFull;
    }

    /**
     * Execute event
     *
     * @param Observer $observer
     * @throws \Exception
     */
    public function execute(Observer $observer)
    {
        $rule = $observer->getEvent()->getData('label_rule');
        if ($rule instanceof RuleInterface) {
            $this->indexerFull->setRule($rule);
            $this->indexerFull->execute();
        }
    }
}
