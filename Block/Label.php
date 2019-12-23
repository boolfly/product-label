<?php
/************************************************************
 * *
 *  * Copyright © Boolfly. All rights reserved.
 *  * See COPYING.txt for license details.
 *  *
 *  * @author    info@boolfly.com
 * *  @project   Product Label
 */
namespace Boolfly\ProductLabel\Block;

use Boolfly\ProductLabel\Helper\Rule;
use Magento\Catalog\Model\Product;
use Magento\Framework\View\Element\Template;

/**
 * Class Label
 *
 * @package Boolfly\ProductLabel\Block
 */
class Label extends Template
{
    /**
     * @var Product
     */
    private $product;

    /**
     * @var string
     */
    protected $_template = 'Boolfly_ProductLabel::label.phtml';

    /**
     * @var Rule
     */
    private $helperRule;

    /**
     * Label constructor.
     *
     * @param Template\Context $context
     * @param Rule             $helperRule
     * @param array            $data
     */
    public function __construct(
        Template\Context $context,
        Rule $helperRule,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->helperRule = $helperRule;
    }

    /**
     * Set Product
     *
     * @param $product
     * @return $this
     */
    public function setProduct($product)
    {
        $this->product = $product;
        return $this;
    }

    /**
     * Get Group Rule
     *
     * @return array
     */
    public function getGroupRule()
    {
        try {
            return $this->helperRule->getAppliedCategoryRule($this->product);
        } catch (\Exception $e) {
            return [];
        }
    }
}
