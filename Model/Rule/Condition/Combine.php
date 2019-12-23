<?php
/************************************************************
 * *
 *  * Copyright © Boolfly. All rights reserved.
 *  * See COPYING.txt for license details.
 *  *
 *  * @author    info@boolfly.com
 * *  @project   Product Label
 */
namespace Boolfly\ProductLabel\Model\Rule\Condition;

use Magento\Rule\Model\Condition\Combine as RuleCombine;
use Magento\Rule\Model\Condition\Context;

/**
 * Class Combine
 *
 * @package Boolfly\ProductLabel\Model\Rule\Condition
 * @method setType(string $type)
 */
class Combine extends RuleCombine
{
    /**
     * @var ProductFactory
     */
    protected $productFactory;

    /**
     * Combine constructor.
     * @param Context        $context
     * @param ProductFactory $conditionFactory
     * @param array          $data
     */
    public function __construct(
        Context $context,
        ProductFactory $conditionFactory,
        array $data = []
    ) {
        $this->productFactory = $conditionFactory;
        parent::__construct($context, $data);
        $this->setType(get_class($this));
    }

    /**
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        $productAttributes = $this->productFactory->create()->loadAttributeOptions()->getAttributeOption();
        $attributes        = [];
        foreach ($productAttributes as $code => $label) {
            $attributes[] = [
                'value' => Product::class . '|' . $code,
                'label' => $label,
            ];
        }
        $conditions = parent::getNewChildSelectOptions();
        $conditions = array_merge_recursive(
            $conditions,
            [
                [
                    'value' => get_class($this),
                    'label' => __('Conditions Combination'),
                ],
                ['label' => __('Product Attribute'), 'value' => $attributes]
            ]
        );
        return $conditions;
    }

    /**
     * @param array $productCollection
     * @return $this
     */
    public function collectValidatedAttributes($productCollection)
    {
        foreach ($this->getConditions() as $condition) {
            /** @var Product|Combine $condition */
            $condition->collectValidatedAttributes($productCollection);
        }
        return $this;
    }
}
