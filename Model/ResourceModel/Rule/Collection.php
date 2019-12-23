<?php
/************************************************************
 * *
 *  * Copyright © Boolfly. All rights reserved.
 *  * See COPYING.txt for license details.
 *  *
 *  * @author    info@boolfly.com
 * *  @project   Product Label
 */
namespace Boolfly\ProductLabel\Model\ResourceModel\Rule;

use Boolfly\ProductLabel\Api\Data\RuleInterface;
use Boolfly\ProductLabel\Model\ResourceModel\Rule as RuleResourceModel;
use Boolfly\ProductLabel\Model\Rule;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Boolfly\ProductLabel\Model\Source\Config\Status;

/**
 * Class Collection
 *
 * @package Boolfly\ProductLabel\Model\ResourceModel\Rule
 */
class Collection extends AbstractCollection
{
    /**
     * Primary column
     *
     * @var string
     */
    protected $_idFieldName = 'rule_id';

    /**
     * Initialize resource collection
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init(Rule::class, RuleResourceModel::class);
    }

    /**
     * Only get enable Rule
     *
     * @return Collection
     */
    public function addActiveStatusFilter()
    {
        return $this->addFieldToFilter('status', Status::STATUS_ENABLED);
    }


    /**
     * Order By Type
     *
     * @param $type
     * @return $this
     */
    public function addOrderByType($type)
    {
        $field = $type == 'category' ? RuleInterface::CATEGORY_POSITION : RuleInterface::PRODUCT_POSITION;
        $this->addOrder($field, 'DESC');
        $this->addOrder(RuleInterface::PRIORITY, 'ASC');

        return $this;
    }

    /**
     * Add Store To Filter
     *
     * @param null $storeId
     * @return $this
     */
    public function addStoreToFilter($storeId = null)
    {
        if ($storeId && is_numeric($storeId)) {
            $conditions = $this->getConnection()->quoteInto(
                'main_table.rule_id = rule_store.rule_id AND rule_store.store_id = ?',
                $storeId
            );
            $this->getSelect()->joinInner(
                ['rule_store' => $this->getTable('boolfly_productlabel_store')],
                $conditions
            );
        }

        return $this;
    }

    /**
     * Add Customer Group To Filter
     *
     * @param null $groupId
     * @return $this
     */
    public function addCustomerGroupToFilter($groupId = null)
    {
        if ($groupId && is_numeric($groupId)) {
            $conditions = $this->getConnection()->quoteInto(
                'main_table.rule_id = rule_customer.rule_id AND rule_customer.customer_group_id = ?',
                $groupId
            );
            $this->getSelect()->joinInner(
                ['rule_customer' => $this->getTable('boolfly_productlabel_customer_group')],
                $conditions
            );
        }

        return $this;
    }
}
