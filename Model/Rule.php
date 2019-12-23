<?php
/************************************************************
 * *
 *  * Copyright © Boolfly. All rights reserved.
 *  * See COPYING.txt for license details.
 *  *
 *  * @author    info@boolfly.com
 * *  @project   Product Label
 */
namespace Boolfly\ProductLabel\Model;

use Boolfly\ProductLabel\Api\Data\RuleInterface;
use Boolfly\ProductLabel\Model\Source\Config\LabelType;
use Boolfly\ProductLabel\Model\Source\Config\Type;
use Magento\Rule\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\DataObject\IdentityInterface;
use Boolfly\ProductLabel\Model\ResourceModel\Rule as RuleResourceModel;
use Boolfly\ProductLabel\Model\Rule\Condition\CombineFactory;
use Magento\Rule\Model\Action\CollectionFactory;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Store\Model\StoreManagerInterface;
use Boolfly\Base\Model\ImageUploader;

/**
 * Class Rule
 *
 * @package Boolfly\ProductLabel\Model
 *
 * @method array getCustomerGroupIds()
 * @method array getStoreIds()
 */
class Rule extends AbstractModel implements RuleInterface, IdentityInterface
{

    /**
     * @var string
     */
    protected $_eventObject = 'label_rule';

    /**
     * Event Prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'boolfly_label_rule';

    /**
     * @var ImageUploader
     */
    protected $imageUploader;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var CollectionFactory
     */
    private $actionCollectionFactory;

    /**
     * @var CombineFactory
     */
    private $combineFactory;

    /**
     * Rule constructor.
     *
     * @param Context               $context
     * @param Registry              $registry
     * @param FormFactory           $formFactory
     * @param TimezoneInterface     $localeDate
     * @param CombineFactory        $combineFactory
     * @param CollectionFactory     $actionCollectionFactory
     * @param StoreManagerInterface $storeManager
     * @param ImageUploader         $imageUploader
     * @param AbstractResource|null $resource
     * @param AbstractDb|null       $resourceCollection
     * @param array                 $relatedCacheTypes
     * @param array                 $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        TimezoneInterface $localeDate,
        CombineFactory $combineFactory,
        CollectionFactory $actionCollectionFactory,
        StoreManagerInterface $storeManager,
        ImageUploader $imageUploader,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $relatedCacheTypes = [],
        array $data = []
    ) {
        $this->combineFactory          = $combineFactory;
        $this->actionCollectionFactory = $actionCollectionFactory;
        $this->storeManager            = $storeManager;
        $this->imageUploader           = $imageUploader;
        parent::__construct(
            $context,
            $registry,
            $formFactory,
            $localeDate,
            $resource,
            $resourceCollection,
            $data
        );
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init(RuleResourceModel::class);
    }

    /**
     * Getter for rule conditions collection
     *
     * @return \Magento\Rule\Model\Condition\Combine
     */
    public function getConditionsInstance()
    {
        return $this->combineFactory->create();
    }

    /**
     * @return \Magento\Rule\Model\Action\Collection
     */
    public function getActionsInstance()
    {
        return $this->actionCollectionFactory->create();
    }

    /**
     * @param string $formName
     * @return string
     */
    public function getConditionsFieldSetId($formName = '')
    {
        return $formName . 'rule_conditions_fieldset_' . $this->getId();
    }

    /**
     * @param string $formName
     * @return string
     */
    public function getActionsFieldSetId($formName = '')
    {
        $this->getActions();
        return $formName . 'rule_actions_fieldset_' . $this->getId();
    }

    /**
     * Return unique ID(s) for each object in system
     *
     * @return string[]
     */
    public function getIdentities()
    {
        $identities = [
            self::CACHE_TAG . '_' . $this->getId(),
        ];

        if (!$this->getId() || $this->isDeleted()) {
            $identities[] = self::CACHE_TAG;
        }

        return array_unique($identities);
    }

    /**
     * Get Title
     *
     * @return string|null
     * @since 1.0.0
     */
    public function getTitle()
    {
        return $this->_getData(self::TITLE);
    }

    /**
     * Set Title
     *
     * @param string $title
     *
     * @return $this
     * @since 1.0.0
     */
    public function setTitle($title)
    {
        return $this->setData(self::TITLE, $title);
    }

    /**
     * Get Type
     *
     * @return string|null
     * @since 1.0.0
     */
    public function getType()
    {
        return (int)$this->_getData(self::TYPE);
    }

    /**
     * Set Type
     *
     * @param string $type
     *
     * @return $this
     * @since 1.0.0
     */
    public function setType($type)
    {
        return $this->setData(self::TYPE, $type);
    }

    /**
     * Get Status
     *
     * @return boolean
     * @since 1.0.0
     */
    public function getStatus()
    {
        return (boolean)$this->_getData(self::STATUS);
    }

    /**
     * Set Status
     *
     * @param integer|boolean $status
     *
     * @return $this
     * @since 1.0.0
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * Get Priority
     *
     * @return mixed
     * @since 1.0.0
     */
    public function getPriority()
    {
        return $this->_getData(self::PRIORITY);
    }

    /**
     * Set Priority
     *
     * @param integer $priority
     * @return $this
     */
    public function setPriority($priority)
    {
        return $this->setData(self::PRIORITY, $priority);
    }

    /**
     * Get Conditions Serialized
     *
     * @return mixed
     */
    public function getConditionsSerialized()
    {
        return $this->_getData(self::CONDITIONS_SERIALIZED);
    }

    /**
     * Set Conditions Serialized
     *
     * @param string $conditions
     * @return $this
     */
    public function setConditionsSerialized($conditions)
    {
        return $this->setData(self::CONDITIONS_SERIALIZED, $conditions);
    }

    /**
     * Display Label in Product List, Category
     *
     * @return boolean
     */
    public function isDisplayInCategory()
    {
        return (boolean)$this->_getData(self::DISPLAY_IN_CATEGORY);
    }

    /**
     * Set Display In Category
     *
     * @param boolean $isDisplay
     * @return $this
     */
    public function setDisplayInCategory($isDisplay)
    {
        return $this->setData(self::DISPLAY_IN_CATEGORY, $isDisplay);
    }

    /**
     * Get Category Type
     *
     * @return integer
     */
    public function getCategoryType()
    {
        return $this->_getData(self::CATEGORY_LABEL_TYPE);
    }

    /**
     * Set Category Type
     *
     * @param integer $type
     * @return $this
     */
    public function setCategoryType($type)
    {
        return $this->setData(self::CATEGORY_LABEL_TYPE, $type);
    }

    /**
     * Get Category Image
     *
     * @return mixed
     */
    public function getCategoryImage()
    {
        return $this->_getData(self::CATEGORY_IMAGE);
    }

    /**
     * Set Category Image
     *
     * @param string $image
     * @return mixed
     */
    public function setCategoryImage($image)
    {
        return $this->setData(self::CATEGORY_LABEL_TYPE, $image);
    }

    /**
     * Text Label in Category
     *
     * @return string
     */
    public function getCategoryText()
    {
        return $this->_getData(self::CAT_TEXT);
    }

    /**
     * Set Text label in Category
     *
     * @param string $text
     * @return $this
     */
    public function setCategoryText($text)
    {
        return $this->setData(self::CAT_TEXT, $text);
    }

    /**
     * Get Css Style in Category
     *
     * @return string
     */
    public function getCssStyleCategory()
    {
        return $this->_getData(self::CSS_STYLE_CATEGORY);
    }

    /**
     * Set Css Style in Category
     *
     * @param string $css
     * @return $this
     */
    public function setCssStyleCategory($css)
    {
        return $this->setData(self::CSS_STYLE_CATEGORY, $css);
    }

    /**
     * Display Label in Product List, Product
     *
     * @return boolean
     */
    public function isDisplayInProduct()
    {
        return (boolean)$this->_getData(self::DISPLAY_IN_PRODUCT);
    }

    /**
     * Set Display In Product
     *
     * @param boolean $isDisplay
     * @return $this
     */
    public function setDisplayInProduct($isDisplay)
    {
        return $this->setData(self::DISPLAY_IN_PRODUCT, $isDisplay);
    }

    /**
     * Get Product Type
     *
     * @return integer
     */
    public function getProductType()
    {
        return $this->_getData(self::PRODUCT_LABEL_TYPE);
    }

    /**
     * Set Product Type
     *
     * @param integer $type
     * @return $this
     */
    public function setProductType($type)
    {
        return $this->setData(self::PRODUCT_LABEL_TYPE, $type);
    }

    /**
     * Get Product Image
     *
     * @return mixed
     */
    public function getProductImage()
    {
        return $this->_getData(self::PRODUCT_IMAGE);
    }

    /**
     * Set Product Image
     *
     * @param string $image
     * @return mixed
     */
    public function setProductImage($image)
    {
        return $this->setData(self::PRODUCT_IMAGE, $image);
    }

    /**
     * Text Label in Product
     *
     * @return string
     */
    public function getProductText()
    {
        return $this->_getData(self::PRODUCT_TEXT);
    }

    /**
     * Set Text label in Product
     *
     * @param string $text
     * @return $this
     */
    public function setProductText($text)
    {
        return $this->setData(self::PRODUCT_TEXT, $text);
    }

    /**
     * Get Css Style in Product
     *
     * @return string
     */
    public function getCssStyleProduct()
    {
        return $this->_getData(self::CSS_STYLE_PRODUCT);
    }

    /**
     * Set Css Style in Product
     *
     * @param string $css
     * @return $this
     */
    public function setCssStyleProduct($css)
    {
        return $this->setData(self::CSS_STYLE_PRODUCT, $css);
    }

    /**
     * Get Category Position
     *
     * @return string
     */
    public function getCategoryPosition()
    {
        return $this->_getData(self::CATEGORY_POSITION);
    }

    /**
     * Set Category Position
     *
     * @param $pos
     * @return $this
     */
    public function setCategoryPosition($pos)
    {
        return $this->setData(self::CATEGORY_POSITION, $pos);
    }

    /**
     * Get Product Position
     *
     * @return string
     */
    public function getProductPosition()
    {
        return $this->_getData(self::PRODUCT_POSITION);
    }

    /**
     * Set Product Position
     *
     * @param $pos
     * @return $this
     */
    public function setProductPosition($pos)
    {
        return $this->setData(self::PRODUCT_POSITION, $pos);
    }

    /**
     * @return boolean
     */
    public function isUseImageInList()
    {
        return $this->getCategoryType() == LabelType::IMAGE_TYPE;
    }

    /**
     * @return boolean
     */
    public function isUseImageInProduct()
    {
        return $this->getProductType() == LabelType::IMAGE_TYPE;
    }

    /**
     * Label Image in Product Listing
     *
     * @return boolean|string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCategoryImageUrl()
    {
        return $this->imageUploader->getImageUrl($this->getCategoryImage());
    }

    /**
     * Label Image in Product Page
     *
     * @return boolean|string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getProductImageUrl()
    {
        return $this->imageUploader->getImageUrl($this->getProductImage());
    }

    /**
     * Get Additional Class
     *
     * @return string
     */
    public function getAdditionalClass()
    {
        $type            = $this->getType();
        $types           = Type::getOptionArray();
        $additionalClass = '';
        if (!empty($types[$type])) {
            $additionalClass = str_replace(' ', '-', strtolower($types[$type]));
        }

        return $additionalClass;
    }
}
