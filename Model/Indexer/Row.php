<?php
/************************************************************
 * *
 *  * Copyright © Boolfly. All rights reserved.
 *  * See COPYING.txt for license details.
 *  *
 *  * @author    info@boolfly.com
 * *  @project   Product Label
 */
namespace Boolfly\ProductLabel\Model\Indexer;

use Boolfly\ProductLabel\Helper\Rule as HelperRule;
use Boolfly\ProductLabel\Model\Rule;
use Boolfly\ProductLabel\Model\Condition\Sql\Builder as SqlConditionBuilder;
use Magento\Catalog\Model\Product\Attribute\Source\Status as ProductStatus;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Framework\App\ResourceConnection;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Catalog\Api\Data\ProductInterface;

/**
 * Class Row
 *
 * @package Boolfly\ProductLabel\Model\Indexer
 */
class Row
{
    /**
     * Index Rule Table
     *
     * @const
     */
    const FLAT_INDEX_RULE_TABLE = 'boolfly_productlabel_product_index_rule';

    /**
     * @var Rule
     */
    protected $rule;

    /**
     * @var ProductCollectionFactory
     */
    private $productCollectionFactory;

    /**
     * @var SqlConditionBuilder
     */
    private $builder;

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var Visibility
     */
    private $productVisibility;

    /**
     * @var string
     */
    private $productEntityIdentifierField;

    /**
     * @var MetadataPool
     */
    private $metadataPool;

    /**
     * @var
     */
    private $productId;
    /**
     * @var HelperRule
     */
    private $helperRule;

    /**
     * Row constructor.
     *
     * @param ProductCollectionFactory $collectionFactory
     * @param ResourceConnection       $resourceConnection
     * @param HelperRule               $helperRule
     * @param Visibility               $productVisibility
     * @param MetadataPool             $metadataPool
     * @param SqlConditionBuilder      $builder
     */
    public function __construct(
        ProductCollectionFactory $collectionFactory,
        ResourceConnection $resourceConnection,
        HelperRule $helperRule,
        Visibility $productVisibility,
        MetadataPool $metadataPool,
        SqlConditionBuilder $builder
    ) {
        $this->productCollectionFactory = $collectionFactory;
        $this->builder                  = $builder;
        $this->resourceConnection       = $resourceConnection;
        $this->productVisibility        = $productVisibility;
        $this->metadataPool             = $metadataPool;
        $this->helperRule               = $helperRule;
    }

    /**
     * Set Rule
     *
     * @param Rule $rule
     * @return $this
     */
    public function setRule(Rule $rule)
    {
        $this->rule = $rule;
        return $this;
    }

    /**
     * Get Rule
     *
     * @return Rule
     */
    public function getRule()
    {
        return $this->rule;
    }

    /**
     * Set Product Id
     *
     * @param $id
     * @return $this
     */
    public function setProductId($id)
    {
        $this->productId = (int)$id;
        return $this;
    }

    /**
     * Get Product Id
     *
     * @return integer
     */
    public function getProductId()
    {
        return $this->productId;
    }

    /**
     * @param null $id
     * @return $this
     * @throws \Exception
     */
    public function execute($id = null)
    {
        $this->setProductId($id);
        $this->cleanData();
        $connection     = $this->resourceConnection->getConnection();
        $ruleCollection = $this->helperRule->getRuleCollection();
        $dataImport     = [];
        /** @var Rule $rule */
        foreach ($ruleCollection as $rule) {
            $productCollection = $this->getProductCollection();
            $productCollection->addFieldToFilter($this->getProductIdentifierField(), $id);
            $this->builder->attachConditionToCollection($productCollection, $rule->getConditions());
            if ($productCollection->getSize() > 0) {
                $dataImport[] = [
                    'rule_id' => $rule->getId(),
                    'product_id' => $id
                ];
            }
        }
        if (!empty($dataImport)) {
            $connection->insertOnDuplicate($connection->getTableName(self::FLAT_INDEX_RULE_TABLE), $dataImport);
        }

        return $this;
    }

    /**
     * @param boolean $isVisibility
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     * @throws \Exception
     */
    protected function getProductCollection($isVisibility = true)
    {
        $productCollection = $this->productCollectionFactory->create();
        $productCollection->addFieldToFilter('status', ProductStatus::STATUS_ENABLED);
        if ($isVisibility) {
            $productCollection->addFieldToFilter('visibility', ['in' => $this->productVisibility->getVisibleInSiteIds()]);
        }
        $productCollection->setOrder($this->getProductIdentifierField());

        return $productCollection;
    }

    /**
     * Clean All Old Data
     */
    private function cleanData()
    {
        $connection = $this->resourceConnection->getConnection();
        $connection->delete(
            $connection->getTableName(self::FLAT_INDEX_RULE_TABLE),
            $connection->quoteInto('product_id = ?', $this->getProductId())
        );
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
