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
 * Class Index
 *
 * @package Boolfly\ProductLabel\Controller\Adminhtml\Rule
 */
class Index extends AbstractRule
{
    /**
     * @return void
     */
    public function execute()
    {
        $this->initAction()->_addBreadcrumb(__('Catalog'), __('Catalog'));
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Manage Rules'));
        $this->_view->renderLayout();
    }
}
