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
 * Class Edit
 *
 * @package Boolfly\ProductLabel\Controller\Adminhtml\Rule
 */
class Edit extends AbstractRule
{

    /**
     * @return void
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        /** @var \Boolfly\ProductLabel\Model\Rule $model */
        $model = $this->ruleFactory->create();
        $this->coreRegistry->register('current_label_rule', $model);
        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addErrorMessage(__('This rule no longer exists.'));
                $this->_redirect('*/rule/*');
                return;
            }
            $model->getConditions()->setFormName('label_rule_form');
            $model->getConditions()->setJsFormObject(
                $model->getConditionsFieldSetId($model->getConditions()->getFormName())
            );
            $model->getActions()->setFormName('label_rule_form');
            $model->getActions()->setJsFormObject(
                $model->getActionsFieldSetId($model->getActions()->getFormName())
            );
        }
        $data = $this->_objectManager->get('Magento\Backend\Model\Session')->getPageData(true);
        if (!empty($data)) {
            $model->addData($data);
        }

        $this->initAction();
        $this->_addBreadcrumb(
            $id ? __('Edit Rule') : __('New Rule'),
            $id ? __('Edit Rule') : __('New Rule')
        );
        $this->_view->getPage()->getConfig()->getTitle()->prepend(
            $model->getId() ? $model->getTitle() : __('New Rule')
        );
        $this->_view->renderLayout();
    }
}
