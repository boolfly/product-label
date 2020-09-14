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

use Boolfly\ProductLabel\Model\Rule;
use Boolfly\ProductLabel\Model\Condition\Sql\Builder as SqlConditionBuilder;
use Magento\Catalog\Model\Product\Attribute\Source\Status as ProductStatus;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Framework\App\ResourceConnection;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Catalog\Api\Data\ProductInterface;

/**
 * Class Full
 *
 * @package Boolfly\ProductLabel\Model\Indexer
 */
class Full
{
    /**
     * Index Rule Table
     *
     * @const
     */
    const FLAT_INDEX_RULE_TABLE = 'boolfly_productlabel_product_index_rule';

    /**
     * Page Size
     */
    const PAGE_SIZE = 200;

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
     * Full constructor.
     *
     * @param ProductCollectionFactory $collectionFactory
     * @param ResourceConnection       $resourceConnection
     * @param Visibility               $productVisibility
     * @param MetadataPool             $metadataPool
     * @param SqlConditionBuilder      $builder
     */
    public function __construct(
        ProductCollectionFactory $collectionFactory,
        ResourceConnection $resourceConnection,
        Visibility $productVisibility,
        MetadataPool $metadataPool,
        SqlConditionBuilder $builder
    ) {
        $this->productCollectionFactory = $collectionFactory;
        $this->builder                  = $builder;
        $this->resourceConnection       = $resourceConnection;
        $this->productVisibility        = $productVisibility;
        $this->metadataPool             = $metadataPool;
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
     * Get all Product map with rule
     *
     * @throws \Exception
     */
    public function execute()
    {
        if ($this->getRule() && $this->getRule()->getId()) {
            $this->cleanData();
            $productCollection = $this->getProductCollection();
            $conditions        = $this->getRule()->getConditions();
            $this->builder->attachConditionToCollection($productCollection, $conditions);
            $productCollection->setPageSize(self::PAGE_SIZE);
            $totalsPage  = $productCollection->getLastPageNumber();
            $connection  = $this->resourceConnection->getConnection();
            $currentPage = 1;
            if ($productCollection->getSize() > 0) {
                do {
                    $productCollection->setCurPage($currentPage);
                    $productCollection->load();
                    $importData = [];
                    foreach ($productCollection as $product) {
                        $importData[] = [
                            'rule_id' => $this->getRule()->getId(),
                            'product_id' => $product->getData($this->getProductIdentifierField())
                        ];
                    }
                    $connection->insertOnDuplicate($connection->getTableName(self::FLAT_INDEX_RULE_TABLE), $importData);
                    $currentPage++;
                    $productCollection->clear();
                } while ($currentPage <= $totalsPage);
            }
        }
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
            $productCollection->setVisibility(
                $this->productVisibility->getVisibleInSiteIds()
            )->addStoreFilter();
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
            $connection->quoteInto('rule_id = ?', $this->getRule()->getId())
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
