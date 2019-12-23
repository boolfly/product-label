<?php
/************************************************************
 * *
 *  * Copyright © Boolfly. All rights reserved.
 *  * See COPYING.txt for license details.
 *  *
 *  * @author    info@boolfly.com
 * *  @project   Product Label
 */
namespace Boolfly\ProductLabel\Controller\Adminhtml\Rule;

use Boolfly\ProductLabel\Model\ResourceModel\Rule\CollectionFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Framework\Controller\ResultFactory;
use Boolfly\ProductLabel\Controller\Adminhtml\AbstractRule;
use Boolfly\ProductLabel\Model\RuleFactory;

/**
 * Class MassDelete
 *
 * @package Boolfly\ProductLabel\Controller\Adminhtml\Rule
 */
class MassDelete extends AbstractRule
{

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var Filter
     */
    private $filter;

    /**
     * MassStatus constructor.
     *
     * @param Context           $context
     * @param Registry          $coreRegistry
     * @param RuleFactory       $ruleFactory
     * @param CollectionFactory $collectionFactory
     * @param Filter            $filter
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        RuleFactory $ruleFactory,
        Filter $filter,
        CollectionFactory $collectionFactory
    ) {
        parent::__construct($context, $coreRegistry, $ruleFactory);
        $this->collectionFactory = $collectionFactory;
        $this->filter            = $filter;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     * @throws LocalizedException
     */
    public function execute()
    {
        $collections = $this->filter->getCollection($this->collectionFactory->create());
        $totals      = 0;
        try {
            /** @var \Boolfly\ProductLabel\Model\Rule $item */
            foreach ($collections as $item) {
                $item->delete();
                $totals++;
            }
            $this->messageManager->addSuccessMessage(__('A total of %1 record(s) have been updated.', $totals));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('Something went wrong while delete the rule(s).'));
        }

        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }
}
