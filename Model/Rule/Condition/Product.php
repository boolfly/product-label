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

use Magento\Backend\Helper\Data;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute;
use Magento\Catalog\Model\ResourceModel\Product as ResourceProduct;
use Magento\Catalog\Model\ResourceModel\Product\Collection as ProductCollection;
use Magento\Eav\Model\Config;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\Collection;
use Magento\Framework\Locale\FormatInterface;
use Magento\Rule\Model\Condition\Context;
use Magento\Rule\Model\Condition\Product\AbstractProduct;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Product
 *
 * @package Boolfly\ProductLabel\Model\Rule\Condition
 *
 * @method string getAttribute() Returns attribute code
 * @method string getJsFormObject()
 * @method \Boolfly\ProductLabel\Model\Rule getRule()
 * @method setAttributeOption(array $option)
 * @method string getId()
 */
class Product extends AbstractProduct
{

    /**
     * @var array
     */
    protected $joinedAttributes = [];

    /**
     * Store manager
     *
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param Context                    $context
     * @param Data                       $backendData
     * @param Config                     $config
     * @param ProductFactory             $productFactory
     * @param ProductRepositoryInterface $productRepository
     * @param ResourceProduct            $productResource
     * @param Collection                 $attrSetCollection
     * @param FormatInterface            $localeFormat
     * @param StoreManagerInterface      $storeManager
     * @param array                      $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Context $context,
        Data $backendData,
        Config $config,
        ProductFactory $productFactory,
        ProductRepositoryInterface $productRepository,
        ResourceProduct $productResource,
        Collection $attrSetCollection,
        FormatInterface $localeFormat,
        StoreManagerInterface $storeManager,
        array $data = []
    ) {
        $this->storeManager = $storeManager;
        parent::__construct(
            $context,
            $backendData,
            $config,
            $productFactory,
            $productRepository,
            $productResource,
            $attrSetCollection,
            $localeFormat,
            $data
        );
    }

    /**
     * {@inheritdoc}
     */
    public function loadAttributeOptions()
    {
        /** @var Attribute[] $productAttributes */
        $productAttributes = $this->_productResource->loadAllAttributes()->getAttributesByCode();

        $attributes = [];
        foreach ($productAttributes as $attribute) {
            if (!$attribute->getFrontendLabel() || $attribute->getFrontendInput() == 'text') {
                continue;
            }
            $attributes[$attribute->getAttributeCode()] = $attribute->getFrontendLabel();
        }

        $this->_addSpecialAttributes($attributes);

        asort($attributes);
        $this->setAttributeOption($attributes);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function _addSpecialAttributes(array &$attributes)
    {
        parent::_addSpecialAttributes($attributes);
        $attributes['sku'] = __('SKU');
    }

    /**
     * Retrieve value element chooser URL
     *
     * @return string
     */
    public function getValueElementChooserUrl()
    {
        $url = false;
        switch ($this->getAttribute()) {
            case 'sku':
            case 'category_ids':
                $url = 'catalog_rule/promo_widget/chooser/attribute/' . $this->getAttribute();
                if ($this->getJsFormObject()) {
                    if ($this->getRule()->getId()) {
                        $url .= '/form/' . $this->getRule()->getConditionsFieldSetId('boolfly_label_rule_form');
                    } else {
                        $url .= '/form/' . $this->getJsFormObject();
                    }
                }
                break;
            default:
                break;
        }
        return $url !== false ? $this->_backendData->getUrl($url) : '';
    }

    /**
     * @param $collection
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function addToCollection($collection)
    {
        $attribute = $this->getAttributeObject();

        if ($collection->isEnabledFlat()) {
            $alias                                                  = array_keys($collection->getSelect()->getPart('from'))[0];
            $this->joinedAttributes[$attribute->getAttributeCode()] = $alias . '.' . $attribute->getAttributeCode();
            return $this;
        }

        if ('category_ids' == $attribute->getAttributeCode() || $attribute->isStatic()) {
            return $this;
        }

        if ($attribute->getBackend() && $attribute->isScopeGlobal()) {
            $this->addGlobalAttribute($attribute, $collection);
        } else {
            $this->addNotGlobalAttribute($attribute, $collection);
        }

        $attributes                                 = $this->getRule()->getCollectedAttributes();
        $attributes[$attribute->getAttributeCode()] = true;
        $this->getRule()->setCollectedAttributes($attributes);

        return $this;
    }

    /**
     * @param Attribute         $attribute
     * @param ProductCollection $collection
     * @return $this
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function addGlobalAttribute(
        Attribute $attribute,
        ProductCollection $collection
    ) {
        $storeId = $this->storeManager->getStore()->getId();

        switch ($attribute->getBackendType()) {
            case 'decimal':
            case 'datetime':
            case 'int':
                $alias = 'at_' . $attribute->getAttributeCode();
                $collection->addAttributeToSelect($attribute->getAttributeCode(), 'inner');
                break;
            default:
                $alias = 'at_'. md5($this->getId()) . $attribute->getAttributeCode();
                $collection->getSelect()->join(
                    [$alias => $collection->getTable('catalog_product_index_eav')],
                    "($alias.entity_id = e.entity_id) AND ($alias.store_id = $storeId)" .
                    " AND ($alias.attribute_id = {$attribute->getId()})",
                    []
                );
        }

        $this->joinedAttributes[$attribute->getAttributeCode()] = $alias . '.value';

        return $this;
    }

    /**
     * @param Attribute         $attribute
     * @param ProductCollection $collection
     * @return $this
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function addNotGlobalAttribute(
        Attribute $attribute,
        ProductCollection $collection
    ) {
        $storeId       = $this->storeManager->getStore()->getId();
        $values        = $collection->getAllAttributeValues($attribute);
        $validEntities = [];
        if ($values) {
            foreach ($values as $entityId => $storeValues) {
                if (isset($storeValues[$storeId])) {
                    if ($this->validateAttribute($storeValues[$storeId])) {
                        $validEntities[] = $entityId;
                    }
                } else {
                    if ($this->validateAttribute($storeValues[Store::DEFAULT_STORE_ID])) {
                        $validEntities[] = $entityId;
                    }
                }
            }
        }
        $this->setOperator('()');
        $this->unsetData('value_parsed');
        if ($validEntities) {
            $this->setData('value', implode(',', $validEntities));
        } else {
            $this->unsetData('value');
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getMappedSqlField()
    {
        $result = '';
        if ($this->getAttribute() == 'category_ids') {
            $result = parent::getMappedSqlField();
        } elseif (isset($this->joinedAttributes[$this->getAttribute()])) {
            $result = $this->joinedAttributes[$this->getAttribute()];
        } elseif ($this->getAttributeObject()->isStatic()) {
            $result = $this->getAttributeObject()->getAttributeCode();
        } elseif ($this->getAttributeObject()->getIsGlobal()) {
            $result = parent::getMappedSqlField();
        } elseif ($this->getValueParsed()) {
            $result = 'e.entity_id';
        }

        return $result;
    }
}
