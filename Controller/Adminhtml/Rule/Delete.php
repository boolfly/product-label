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

use Boolfly\ProductLabel\Controller\Adminhtml\AbstractRule;

/**
 * Class Delete
 *
 * @package Boolfly\ProductLabel\Controller\Adminhtml\Rule
 */
class Delete extends AbstractRule
{
    /**
     * @return $this|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $id = (int)$this->getRequest()->getParam('id');
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($id) {
            /** @var \Boolfly\ProductLabel\Model\Rule $model */
            $model = $this->ruleFactory->create();
            try {
                $model->load($id);
                $model->delete();
                $this->messageManager->addSuccessMessage(__('The rule has been deleted.'));
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__('Something went wrong while deleted the rule.'));
                $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e->getMessage());
            }
        }

        return $resultRedirect->setPath('*/*/');
    }
}
