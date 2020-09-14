<?php
/************************************************************
 * *
 *  * Copyright © Boolfly. All rights reserved.
 *  * See COPYING.txt for license details.
 *  *
 *  * @author    info@boolfly.com
 * *  @project   Product Label
 */
namespace Boolfly\ProductLabel\Model\Condition\Sql;

use Boolfly\ProductLabel\Model\Rule\Condition\Product;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Select;
use Magento\Rule\Model\Condition\AbstractCondition;
use Magento\Rule\Model\Condition\Combine;
use \Magento\Eav\Model\Entity\Collection\AbstractCollection;
use Magento\Eav\Model\Config as EavConfig;
use Magento\Store\Model\Store;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Catalog\Api\Data\ProductInterface;
use Zend_Db_Select;

/**
 * Class Builder
 *
 * @package Boolfly\ProductLabel\Model\Condition\Sql
 */
class Builder
{

    /**@#%
     * Fake value
     *
     * @const
     */
    const FAKE_VALUE = 99999;

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected $connection;

    /**
     * @var array
     */
    protected $conditionOperatorMaps = [
        '=='    => 'eq',
        '!='    => 'neq',
        '>='    => 'gteq',
        '>'     => 'gt',
        '<='    => 'lteq',
        '<'     => 'lt',
        '{}'    => 'in',
        '!{}'   => 'nin',
        '()'    => 'in',
        '!()'   => 'nin',
    ];

    protected $stockStatusAttributeCode = 'quantity_and_stock_status';


    /**
     * @var ExpressionFactory
     */
    protected $expressionFactory;

    /**
     * @var EavConfig
     */
    protected $eavConfig;

    /**
     * @var string
     */
    protected $productEntityIdentifierField;

    /**
     * @var MetadataPool
     */
    protected $metadataPool;

    /**
     * Builder constructor.
     *
     * @param ExpressionFactory  $expressionFactory
     * @param ResourceConnection $resourceConnection
     * @param MetadataPool       $metadataPool
     * @param EavConfig          $eavConfig
     */
    public function __construct(
        ExpressionFactory $expressionFactory,
        ResourceConnection $resourceConnection,
        MetadataPool $metadataPool,
        EavConfig $eavConfig
    ) {
        $this->expressionFactory = $expressionFactory;
        $this->connection        = $resourceConnection->getConnection();
        $this->eavConfig         = $eavConfig;
        $this->metadataPool      = $metadataPool;
    }

    /**
     * @param AbstractCollection $collection
     * @param Combine            $combine
     * @throws \Exception
     */
    public function attachConditionToCollection(
        AbstractCollection $collection,
        Combine $combine
    ) {
        $joinTables = $this->getCombineTablesToJoin($combine);
        $this->joinTablesToCollection($collection, $joinTables);
        $whereExpression = (string)$this->getMappedSqlCombination($combine);
        if (!empty($whereExpression)) {
            $collection->getSelect()->where($whereExpression);
        }
    }

    /**
     * Get Connection
     *
     * @return \Magento\Framework\DB\Adapter\AdapterInterface
     */
    private function getConnection()
    {
        return $this->connection;
    }

    /**
     * @param Combine $combine
     * @return array
     * @throws \Exception
     */
    protected function getCombineTablesToJoin(Combine $combine)
    {
        $joinTables = [];

        /** @var Product|Combine $condition */
        foreach ($combine->getConditions() as $condition) {
            if ($condition instanceof Combine) {
                $joinTables[] = ['sub' => $this->getCombineTablesToJoin($condition)];
            } else {
                $attribute = $condition->getAttributeObject();
                if ($attribute->getId()) {
                    if ($attribute->getAttributeCode() === $this->stockStatusAttributeCode) {
                        $joinTables[] = $this->getTableJoinStockStatus();
                    } elseif ($attribute->getBackendType() != 'static') {
                        $joinTables[] = $this->getTableJoin($attribute);
                    }
                }
            }
        }

        return $joinTables;
    }

    /**
     * Get Info to join cataloginventory_stock_status
     *
     * @return array
     * @throws \Exception
     */
    private function getTableJoinStockStatus()
    {
        $alias                  = 'stock_status';
        $connection             = $this->getConnection();
        $productIdentifierField = $this->getProductIdentifierField();
        $joinCondition          = $connection->quoteColumnAs($alias . '.product_id', null)
            . ' = ' . $connection->quoteColumnAs('e.'. $productIdentifierField, null);

        return [
            'alias' => $alias,
            'table' => 'cataloginventory_stock_status',
            'column' => $alias . '.stock_status' . ' AS stock_status',
            'conditions' => [
                $joinCondition
            ]
        ];
    }

    /**
     * @param \Magento\Catalog\Model\ResourceModel\Eav\Attribute $attribute
     * @return array
     * @throws \Exception
     */
    protected function getTableJoin($attribute)
    {
        $connection             = $this->getConnection();
        $alias                  = $this->getAlias($attribute->getAttributeCode());
        $productIdentifierField = $this->getProductIdentifierField();
        $joinCondition          = $connection->quoteColumnAs($alias . '.' . $productIdentifierField, null)
            . ' = ' . $connection->quoteColumnAs('e.'. $productIdentifierField, null);

        return [
            'alias' => $alias,
            'table' => 'catalog_product_entity_' . $attribute->getBackendType(),
            'column' => $this->checkIfExistColumn($alias) . ' AS ' . $attribute->getAttributeCode(),
            'conditions' => [
                $joinCondition,
                $connection->quoteInto($alias . '.attribute_id = ?', $attribute->getId()),
                $connection->quoteInto($alias . '.store_id = ?', Store::DEFAULT_STORE_ID),
            ]
        ];
    }

    /**
     * Get Alias
     *
     * @param $attributeCode
     * @return string
     */
    protected function getAlias($attributeCode)
    {
        return 'at_'. $attributeCode;
    }

    /**
     * Get Expression Column
     *
     * @param $alias
     * @return Expression
     */
    protected function checkIfExistColumn($alias)
    {
        $out = 'IF(' . $alias . '.value_id > 0, '. $alias .'.value, null)';
        return $this->expressionFactory->create(['expression' => $out]);
    }

    /**
     * Join tables from conditions combination to collection
     *
     * @param AbstractCollection $collection
     * @param $joinTables
     * @param array $joined
     * @throws \Zend_Db_Select_Exception
     */
    protected function joinTablesToCollection(AbstractCollection $collection, $joinTables, $joined = [])
    {
        $joined = array_merge_recursive(
            array_keys($collection->getSelect()->getPart(Zend_Db_Select::FROM)),
            $joined
        );
        if (is_array($joinTables)) {
            foreach ($joinTables as $table) {
                if (isset($table['sub'])) {
                    $this->joinTablesToCollection($collection, $table['sub'], $joined);
                } else {
                    if (isset($table['alias'])
                        && !in_array($table['alias'], $joined)
                        && !empty($table)
                    ) {
                        $cond      = implode(' AND ', $table['conditions']);
                        $tableName = $this->connection->getTableName($table['table']);
                        $collection->getSelect()->joinLeft([$table['alias'] => $tableName], $cond, $table['column']);
                        $joined[] = $table['alias'];
                    }
                }
            }
        }
    }

    /**
     * Mapped Sql Combination
     *
     * @param Combine $combine
     * @param string  $value
     * @return Expression
     * @throws \Exception
     */
    protected function getMappedSqlCombination(Combine $combine, $value = '')
    {
        $out           = $value ? $value : '';
        $value         = $combine->getValue() ? '' : ' NOT ';
        $getAggregator = $combine->getAggregator();
        $conditions    = $combine->getConditions();
        /** @var AbstractCondition|Combine $condition */
        foreach ($conditions as $key => $condition) {
            $con = $getAggregator == 'any' ? Select::SQL_OR : Select::SQL_AND;
            $con = isset($conditions[$key + 1]) ? $con : '';
            if ($condition instanceof Combine) {
                $out .= $this->getMappedSqlCombination($condition, $value);
            } else {
                $out .= ' ' . $this->getMappedSqlCondition($condition, $value);
            }
            $out .= $out ? (' ' . $con) : '';
        }
        return $this->expressionFactory->create(['expression' => $out]);
    }

    /**
     * Mapped Sql Condition
     *
     * @param AbstractCondition $condition
     * @param string            $out
     * @return string
     * @throws \Exception
     */
    protected function getMappedSqlCondition(AbstractCondition $condition, $out = '')
    {
        /** @var Product $condition */
        $attribute = $condition->getAttributeObject();
        if ($attribute->getId()) {
            $value      = $condition->getValue();
            $connection = $this->getConnection();
            $operator   = $condition->getOperator();
            $alias      = $this->getAlias($attribute->getAttributeCode());
            $column     = $this->checkIfExistColumn($alias);
            if ($attribute->getAttributeCode() === $this->stockStatusAttributeCode) {
                $column = 'stock_status.stock_status';
            }
            $conditionOperator = $this->getConditionOperator($operator);
            if ($attribute->getBackendType() !== 'static' && $conditionOperator) {
                if ($attribute->getFrontendInput() === 'multiselect') {
                    return $this->getMappedSqlConditionMultiselect($operator, $column, $value);
                } elseif (is_string($value)) {
                    return $connection->prepareSqlCondition($column, [$conditionOperator => $value]);
                }
            } elseif ($attribute->getAttributeCode() == 'sku') {
                $value = strpos('in', $conditionOperator) === false ?: $condition->getValueParsed();
                return $connection->prepareSqlCondition($attribute->getAttributeCode(), [$conditionOperator => $value]);
            } elseif ($attribute->getAttributeCode() == 'category_ids') {
                return $this->getMappedSqlConditionCategoryIds($operator, $condition->getValueParsed());
            }
        }
        return $out;
    }

    /**
     * @param $operator
     * @return mixed|null
     */
    private function getConditionOperator($operator)
    {
        return isset($this->conditionOperatorMaps[$operator]) ? $this->conditionOperatorMaps[$operator] : null;
    }

    /**
     * Mapped Sql Condition Multiselect
     *
     * @param $operator
     * @param $column
     * @param $value
     * @return string
     */
    protected function getMappedSqlConditionMultiselect($operator, $column, $value)
    {
        if (is_array($value)) {
            $conditionOperator = strpos($operator, '!') === false ? 'finset' : 'nfinset';
            $multiValue        = [];
            foreach ($value as $val) {
                $multiValue[] = $this->connection->prepareSqlCondition($column, [$conditionOperator => $val]);
            }
            $cond = strpos($operator, '()') !== false ? ' OR ' : ' AND ';
            return implode($cond, $multiValue);
        }

        return '';
    }

    /**
     * Sql Condition for Category Ids
     *
     * @param $operator
     * @param $valueParsed
     * @return mixed
     * @throws \Exception
     */
    protected function getMappedSqlConditionCategoryIds($operator, $valueParsed)
    {
        if (is_array($valueParsed) && !empty($valueParsed)) {
            $conditionCategory = $this->connection->select()
                ->from(
                    $this->connection->getTableName('catalog_category_product'),
                    ['product_id']
                )->where(
                    'category_id IN (?)',
                    $valueParsed
                )->__toString();
            if ($operator == '==') {
                $operator = '{}';
            }
            if ($operator == '!=') {
                $operator = '!{}';
            }
            $productIdentifierField = $this->connection->quoteColumnAs('e.' . $this->getProductIdentifierField(), null);
            $conditionOperator      = $this->getConditionOperator($operator);
            $sqlCondition           = $this->connection->prepareSqlCondition($productIdentifierField, [$conditionOperator => self::FAKE_VALUE]);
            return str_replace(self::FAKE_VALUE, $conditionCategory, $sqlCondition);
        }

        return '';
    }

    /**
     * @return MetadataPool
     */
    protected function getMetadataPool()
    {
        return $this->metadataPool;
    }

    /**
     * Get product entity identifier field
     *
     * @return string
     * @throws \Exception
     */
    private function getProductIdentifierField()
    {
        if ($this->productEntityIdentifierField === null) {
            $this->productEntityIdentifierField = $this->getMetadataPool()
                ->getMetadata(ProductInterface::class)
                ->getIdentifierField();
        }
        return $this->productEntityIdentifierField;
    }
}
