<?php
/************************************************************
 * *
 *  * Copyright © Boolfly. All rights reserved.
 *  * See COPYING.txt for license details.
 *  *
 *  * @author    info@boolfly.com
 * *  @project   Product Label
 */
namespace Boolfly\ProductLabel\Block\Product;

use Boolfly\ProductLabel\Helper\Rule;
use Boolfly\ProductLabel\Model\Config;
use Magento\Catalog\Model\Product;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;

/**
 * Class Label
 *
 * @package Boolfly\ProductLabel\Block
 */
class Label extends Template
{
    /**
     * @var string
     */
    protected $_template = 'Boolfly_ProductLabel::product/label.phtml';

    /**
     * @var Rule
     */
    private $helperRule;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var Config
     */
    private $config;

    /**
     * Label constructor.
     *
     * @param Template\Context $context
     * @param Registry $registry
     * @param Rule $helperRule
     * @param Config $config
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Registry $registry,
        Rule $helperRule,
        Config $config,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->helperRule = $helperRule;
        $this->registry   = $registry;
        $this->config = $config;
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        return $this->config->isEnable() ? parent::_toHtml() : '';
    }

    /**
     * Get Product
     *
     * @return mixed
     */
    public function getProduct()
    {
        return $this->registry->registry('current_product');
    }

    /**
     * Get Group Rule
     *
     * @return array
     */
    public function getGroupRule()
    {
        try {
            return $this->helperRule->getAppliedProductRule($this->getProduct());
        } catch (\Exception $e) {
            return [];
        }
    }
}
