<?php
/************************************************************
 * *
 *  * Copyright © Boolfly. All rights reserved.
 *  * See COPYING.txt for license details.
 *  *
 *  * @author    info@boolfly.com
 * *  @project   Product Label
 */
namespace Boolfly\ProductLabel\Helper;

use Boolfly\ProductLabel\Api\Data\RuleInterface;
use Boolfly\ProductLabel\Model\ResourceModel\Rule\Collection;
use Boolfly\ProductLabel\Model\ResourceModel\Rule\CollectionFactory;
use Boolfly\ProductLabel\Model\Source\Config\Type;
use Magento\Catalog\Model\Product;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Store\Model\StoreManagerInterface;
use Zend_Db_Expr;
use Boolfly\ProductLabel\Model\Indexer\Row as IndexerRow;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\ResourceModel\Product\Collection as ProductCollection;
use Magento\Catalog\Pricing\Price;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Customer\Model\Context as CustomerContext;

/**
 * Class Rule
 *
 * @package Boolfly\ProductLabel\Helper
 */
class Rule
{
    /**@#%
     * @const
     */
    const ALIAS_LABEL_RULE_ID = 'label_rule_ids';

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var Collection
     */
    private $ruleCollection;

    /**
     * @var DateTime
     */
    private $dateTime;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var MetadataPool
     */
    private $metadataPool;

    /**
     * @var string
     */
    private $productEntityIdentifierField;

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var HttpContext
     */
    private $httpContext;

    /**
     * Rule constructor.
     *
     * @param CollectionFactory $collectionFactory
     * @param StoreManagerInterface $storeManager
     * @param ResourceConnection $resourceConnection
     * @param MetadataPool $metadataPool
     * @param HttpContext $httpContext
     * @param DateTime $dateTime
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        StoreManagerInterface $storeManager,
        ResourceConnection $resourceConnection,
        MetadataPool $metadataPool,
        HttpContext $httpContext,
        DateTime $dateTime
    ) {
        $this->collectionFactory  = $collectionFactory;
        $this->dateTime           = $dateTime;
        $this->storeManager       = $storeManager;
        $this->metadataPool       = $metadataPool;
        $this->resourceConnection = $resourceConnection;
        $this->httpContext = $httpContext;
    }

    /**
     * Get Customer Group Id
     *
     * @return int
     */
    private function getCustomerGroupId()
    {
        return (int)$this->httpContext->getValue(CustomerContext::CONTEXT_GROUP);
    }

    /**
     * Get Rule Collection
     *
     * @param boolean $storeFilter
     * @param boolean $groupFilter
     * @return $this|Collection
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getRuleCollection($storeFilter = false, $groupFilter = false)
    {
        if ($this->ruleCollection === null) {
            $dateTime  = $this->dateTime->gmtDate();
            $startDate = [
                'or' => [
                    [
                        'date' => true,
                        'to' => $dateTime,
                    ],
                    ['is' => new Zend_Db_Expr('null')]
                ]
            ];
            $endDate   = [
                'or' => [
                    [
                        'date' => true,
                        'from' => $dateTime,
                    ],
                    ['is' => new Zend_Db_Expr('null')]
                ]
            ];

            $collection = $this->collectionFactory->create()
                ->addActiveStatusFilter()
                ->addFieldToFilter('from_date', $startDate)
                ->addFieldToFilter('to_date', $endDate);
            if ($storeFilter) {
                $collection->addStoreToFilter($this->storeManager->getStore()->getId());
            }
            if ($groupFilter) {
                $collection->addCustomerGroupToFilter($this->getCustomerGroupId());
            }

            $this->ruleCollection = $collection;
        }

        return $this->ruleCollection;
    }

    /**
     * Get Apply Category Rule
     *
     * @param Product $product
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getAppliedCategoryRule(Product $product)
    {
        return $this->getAppliedRule($product, 'cat');
    }

    /**
     * Get Applied Product Rule
     *
     * @param Product $product
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getAppliedProductRule(Product $product)
    {
        return $this->getAppliedRule($product);
    }

    /**
     * Get Applied Rule for Product
     *
     * @param Product $product
     * @param $type
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getAppliedRule(Product $product, $type = 'product')
    {
        $ruleIds = $this->getRuleIdsValue($product);
        $result  = [];
        if (!empty($ruleIds)) {
            $displayField = $type == 'product' ? RuleInterface::DISPLAY_IN_PRODUCT : RuleInterface::DISPLAY_IN_CATEGORY;
            $position     = $type == 'product' ? RuleInterface::PRODUCT_POSITION : RuleInterface::CATEGORY_POSITION;
            $collection   = $this->getRuleCollection(true, true);
            $collection->addFieldToFilter($displayField, 1);
            $collection->addOrderByType($type);
            /** @var \Boolfly\ProductLabel\Model\Rule $rule */
            foreach ($collection as $rule) {
                if (in_array($rule->getId(), $ruleIds)) {
                    if ($rule->getType() == Type::SALE_LABEL && !$this->isSale($product)) {
                        continue;
                    }
                    $result[$rule->getData($position)][] = $rule;
                }
            }
        }

        return $result;
    }

    /**
     * Get All Rule Ids for Product
     *
     * @param Product $product
     * @return array
     */
    public function getRuleIdsValue(Product $product)
    {
        $value = (string)$product->getData(self::ALIAS_LABEL_RULE_ID);
        return $value ? array_unique(explode(',', $value)) : [];
    }

    /**
     * Check Is Sale Product
     *
     * @param Product $product
     * @return mixed
     */
    private function isSale(Product $product)
    {
        $regularPrice = $product->getPriceInfo()
            ->getPrice(Price\RegularPrice::PRICE_CODE)
            ->getAmount()
            ->getValue();
        $finalPrice   = $product->getPriceInfo()
            ->getPrice(Price\FinalPrice::PRICE_CODE)
            ->getAmount()
            ->getValue();

        if ($regularPrice > $finalPrice) {
            return true;
        }

        return false;
    }

    /**
     * Add Rule Ids to Product Collection
     *
     * @param ProductCollection $collection
     * @throws \Exception
     */
    public function addRuleIdsToCollection($collection)
    {
        if ($collection instanceof ProductCollection && !$collection->isLoaded()) {
            $connection = $collection->getConnection();
            $col        = new Zend_Db_Expr('GROUP_CONCAT(rule_id)');
            $cond       = $connection->quoteColumnAs('e.' . $this->getProductIdentifierField(), null)
                . ' = ' . $connection->quoteColumnAs('label_rule.product_id', null);
            $select     = $connection->select()
                ->from($connection->getTableName(IndexerRow::FLAT_INDEX_RULE_TABLE),
                    ['product_id', self::ALIAS_LABEL_RULE_ID => $col]
                )->group('product_id');
            $collection->getSelect()
                ->joinLeft(
                    ['label_rule' => $select],
                    $cond,
                    self::ALIAS_LABEL_RULE_ID
            );
        }
    }

    /**
     * Add Rule Ids to Product
     *
     * @param Product $product
     * @throws \Exception
     */
    public function addRuleIds(Product $product)
    {
        if ($product->getData(self::ALIAS_LABEL_RULE_ID) === null) {
            $productId  = $product->getId();
            $connection = $this->resourceConnection->getConnection();
            $column     = new Zend_Db_Expr('GROUP_CONCAT(rule_id)');
            $select     = $connection->select()->from(
                $connection->getTableName(IndexerRow::FLAT_INDEX_RULE_TABLE),
                $column
            )->where('product_id = ?', (int)$productId);
            $product->setData(self::ALIAS_LABEL_RULE_ID, $connection->fetchOne($select));
        }
    }

    /**
     * Get Meta Data
     *
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
