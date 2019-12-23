<?php
/************************************************************
 * *
 *  * Copyright © Boolfly. All rights reserved.
 *  * See COPYING.txt for license details.
 *  *
 *  * @author    info@boolfly.com
 * *  @project   Product Label
 */
namespace Boolfly\ProductLabel\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Boolfly\ProductLabel\Model\RuleFactory;

/**
 * Class AbstractRule
 *
 * @package Boolfly\ProductLabel\Controller\Adminhtml
 */
abstract class AbstractRule extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Boolfly_ProductLabel::rule';

    /**
     * Core registry
     *
     * @var Registry
     */
    protected $coreRegistry = null;

    /**
     * @var RuleFactory
     */
    protected $ruleFactory;

    /**
     * AbstractRule constructor.
     *
     * @param Context     $context
     * @param Registry    $coreRegistry
     * @param RuleFactory $ruleFactory
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        RuleFactory $ruleFactory
    ) {
        parent::__construct($context);
        $this->coreRegistry = $coreRegistry;
        $this->ruleFactory  = $ruleFactory;
    }

    /**
     * Init action
     *
     * @return $this
     */
    protected function initAction()
    {
        $this->_view->loadLayout();
        $this->_setActiveMenu(
            'Boolfly_ProductLabel::rule'
        )->_addBreadcrumb(
            __('Manage Rule'),
            __('Manage Rule')
        );
        return $this;
    }
}
