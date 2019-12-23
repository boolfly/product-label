<?php
/************************************************************
 * *
 *  * Copyright © Boolfly. All rights reserved.
 *  * See COPYING.txt for license details.
 *  *
 *  * @author    info@boolfly.com
 * *  @project   Product Label
 */
namespace Boolfly\ProductLabel\Model\ResourceModel;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Stdlib\DateTime\DateTime;

/**
 * Class Rule
 *
 * @package Boolfly\ProductLabel\Model\ResourceModel
 */
class Rule extends AbstractDb
{

    /**
     * @var DateTime
     */
    private $dateTime;

    /**
     * Rule constructor.
     *
     * @param Context  $context
     * @param DateTime $dateTime
     * @param null     $connectionName
     */
    public function __construct(
        Context $context,
        DateTime $dateTime,
        $connectionName = null
    ) {
        parent::__construct($context, $connectionName);
        $this->dateTime = $dateTime;
    }

    /**
     * Initialize resource
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('boolfly_productlabel_rule', 'rule_id');
    }

    /**
     * @param AbstractModel $object
     * @return mixed
     */
    protected function _afterLoad(AbstractModel $object)
    {
        $this->getLinkData($object);
        return parent::_afterLoad($object);
    }

    /**
     * Get Link Data
     *
     * @param AbstractModel $object
     */
    private function getLinkData(AbstractModel $object)
    {
        $this->getStoreLink($object);
        $this->getCustomerGroupLink($object);
    }

    /**
     * @param AbstractModel $object
     */
    protected function getStoreLink($object)
    {
        $stores = $this->lookupStoreIds($object->getId());
        $object->setData('store_ids', $stores);
    }

    /**
     * @param AbstractModel $object
     */
    protected function getCustomerGroupLink($object)
    {
        $stores = $this->lookupCustomerGroupsIds($object->getId());
        $object->setData('customer_group_ids', $stores);
    }

    /**
     * Get Label Store Table
     *
     * @return string
     */
    private function getStoreTable()
    {
        return $this->getTable('boolfly_productlabel_store');
    }

    /**
     * Get Customer Group Table Name
     *
     * @return string
     */
    private function getCustomerGroupsTable()
    {
        return $this->getTable('boolfly_productlabel_customer_group');
    }

    /**
     * Before save
     *
     * @param AbstractModel $object
     * @return mixed
     */
    protected function _beforeSave(AbstractModel $object)
    {
        $gmtDate = $this->dateTime->gmtDate();
        if ($object->isObjectNew()) {
            $object->setData('created_at', $gmtDate);
        }
        $object->setData('updated_at', $gmtDate);

        return parent::_beforeSave($object);
    }

    /**
     * @param AbstractModel $object
     * @return mixed
     */
    protected function _afterSave(AbstractModel $object)
    {
        $this->processLinkTable($object);
        return parent::_afterSave($object);
    }

    /**
     * Process data to link table
     *
     * @param AbstractModel $object
     * @return $this
     */
    private function processLinkTable(AbstractModel $object)
    {
        $this->processStoreTable($object);
        $this->processCustomerGroupTable($object);

        return $this;
    }

    /**
     * Save data to boolfly_productlabel_store table
     *
     * @param \Boolfly\ProductLabel\Model\Rule|AbstractModel $object
     */
    private function processStoreTable(AbstractModel $object)
    {
        $oldIds = $this->lookupStoreIds($object->getId());
        $newIds = (array)$object->getStoreIds();
        $this->updateForeignKey(
            $object->getId(),
            $newIds,
            $oldIds,
            $this->getStoreTable(),
            'store_id'
        );
    }

    /**
     * Save data to boolfly_productlabel_store table
     *
     * @param \Boolfly\ProductLabel\Model\Rule|AbstractModel $object
     */
    private function processCustomerGroupTable(AbstractModel $object)
    {
        $oldIds = $this->lookupCustomerGroupsIds($object->getId());
        $newIds = (array)$object->getCustomerGroupIds();
        $this->updateForeignKey(
            $object->getId(),
            $newIds,
            $oldIds,
            $this->getCustomerGroupsTable(),
            'customer_group_id'
        );
    }

    /**
     * Get Store ids to which specified item is assigned
     *
     * @param integer $id
     * @return array
     */
    public function lookupStoreIds($id)
    {
        return $this->lookupIds(
            $id,
            $this->getStoreTable(),
            'store_id'
        );
    }

    /**
     * @param $id
     * @return array
     */
    protected function lookupCustomerGroupsIds($id)
    {
        return $this->lookupIds(
            $id,
            $this->getCustomerGroupsTable(),
            'customer_group_id'
        );
    }

    /**
     * Get ids to which specified item is assigned
     *
     * @param  integer $id
     * @param  string  $tableName
     * @param  string  $field
     * @return array
     */
    protected function lookupIds($id, $tableName, $field)
    {
        $adapter = $this->getConnection();

        $select = $adapter->select()->from(
            $this->getTable($tableName),
            $field
        )->where(
            'rule_id = ?',
            (int)$id
        );

        return $adapter->fetchCol($select);
    }

    /**
     * @param $ruleId
     * @param array  $newIds
     * @param array  $oldIds
     * @param $table
     * @param $field
     */
    protected function updateForeignKey(
        $ruleId,
        array $newIds,
        array $oldIds,
        $table,
        $field
    ) {
        $insert = array_diff($newIds, $oldIds);
        $delete = array_diff($oldIds, $newIds);
        if ($delete) {
            $where = [
                'rule_id = ?'    => (int)$ruleId,
                $field.' IN (?)' => $delete,
            ];
            $this->getConnection()->delete($table, $where);
        }
        if ($insert) {
            $data = [];
            foreach ($insert as $storeId) {
                $data[] = [
                    'rule_id' => (int)$ruleId,
                    $field    => (int)$storeId,
                ];
            }

            $this->getConnection()->insertMultiple($table, $data);
        }
    }
}
