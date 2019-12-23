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
class NewAction extends AbstractRule
{
    /**
     * New rule action
     *
     * @return void
     */
    public function execute()
    {
        $this->_forward('edit');
    }
}
