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

use Magento\Rule\Model\Condition\AbstractCondition;
use Boolfly\ProductLabel\Controller\Adminhtml\AbstractRule;

/**
 * Class NewConditionHtml
 *
 * @package Boolfly\ProductLabel\Controller\Adminhtml\Rule
 */
class NewConditionHtml extends AbstractRule
{
    /**
     * @return void
     */
    public function execute()
    {
        $id       = $this->getRequest()->getParam('id');
        $formName = $this->getRequest()->getParam('form_namespace');
        $typeArr  = explode('|', str_replace('-', '/', $this->getRequest()->getParam('type')));
        $type     = $typeArr[0];

        $model = $this->_objectManager->create($type)
            ->setId($id)
            ->setType($type)
            ->setRule($this->ruleFactory->create())
            ->setPrefix('conditions');

        if (!empty($typeArr[1])) {
            $model->setAttribute($typeArr[1]);
        }

        if ($model instanceof AbstractCondition) {
            $model->setJsFormObject($this->getRequest()->getParam('form'));
            $model->setFormName($formName);
            $html = $model->asHtmlRecursive();
        } else {
            $html = '';
        }
        $this->getResponse()->setBody($html);
    }
}
