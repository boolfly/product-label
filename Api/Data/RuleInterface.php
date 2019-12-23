<?php
/************************************************************
 * *
 *  * Copyright © Boolfly. All rights reserved.
 *  * See COPYING.txt for license details.
 *  *
 *  * @author    info@boolfly.com
 * *  @project   Product Label
 */
namespace Boolfly\ProductLabel\Api\Data;

/**
 * Interface RuleInterface
 *
 * @package Boolfly\ProductLabel\Api\Data
 */
interface RuleInterface
{

    /**#@+
     * Constants Cache Tag
     */
    const CACHE_TAG = 'boolfly_product_label';

    /**#@+
     * Constants defined for keys of data array
     */
    const RULE_ID = 'rule_id';

    const TITLE = 'title';

    const TYPE = 'type';

    const STATUS = 'status';

    const DESCRIPTION = 'description';

    const PRIORITY = 'priority';

    const FROM_DATE = 'from_date';

    const TO_DATE = 'to_date';

    const CONDITIONS_SERIALIZED = 'conditions_serialized';

    const DISPLAY_IN_CATEGORY = 'display_in_cat';

    const CATEGORY_POSITION = 'cat_position';

    const CATEGORY_LABEL_TYPE = 'cat_label_type';

    const CATEGORY_IMAGE = 'cat_image';

    const CAT_TEXT = 'cat_text';

    const CSS_STYLE_CATEGORY = 'css_style_cat';

    const DISPLAY_IN_PRODUCT = 'display_in_product';

    const PRODUCT_POSITION = 'product_position';

    const PRODUCT_LABEL_TYPE = 'product_label_type';

    const PRODUCT_IMAGE = 'product_image';

    const PRODUCT_TEXT = 'product_text';

    const CSS_STYLE_PRODUCT = 'css_style_product';

    const CREATED_AT = 'created_at';

    const UPDATED_AT = 'updated_at';

    /**
     * Get Rule Id
     *
     * @return string|null
     * @since 1.0.0
     */
    public function getId();

    /**
     * Get Title
     *
     * @return string|null
     * @since 1.0.0
     */
    public function getTitle();

    /**
     * Set Title
     *
     * @param string $title
     *
     * @return $this
     * @since 1.0.0
     */
    public function setTitle($title);

    /**
     * Get Type
     *
     * @return integer
     * @since 1.0.0
     */
    public function getType();

    /**
     * Set Type
     *
     * @param integer $type
     *
     * @return $this
     * @since 1.0.0
     */
    public function setType($type);

    /**
     * Get Status
     *
     * @return boolean
     * @since 1.0.0
     */
    public function getStatus();

    /**
     * Set Status
     *
     * @param integer|boolean $status
     *
     * @return $this
     * @since 1.0.0
     */
    public function setStatus($status);

    /**
     * Get Priority
     *
     * @return mixed
     * @since 1.0.0
     */
    public function getPriority();

    /**
     * Set Priority
     *
     * @param integer $priority
     * @return $this
     */
    public function setPriority($priority);

    /**
     * Get Conditions Serialized
     *
     * @return mixed
     */
    public function getConditionsSerialized();

    /**
     * Set Conditions Serialized
     *
     * @param string $conditions
     * @return $this
     */
    public function setConditionsSerialized($conditions);

    /**
     * Display Label in Product List, Category
     *
     * @return boolean
     */
    public function isDisplayInCategory();

    /**
     * Set Display In Category
     *
     * @param boolean $isDisplay
     * @return $this
     */
    public function setDisplayInCategory($isDisplay);

    /**
     * Get Category Position
     *
     * @return string
     */
    public function getCategoryPosition();

    /**
     * Set Category Position
     *
     * @param $pos
     * @return $this
     */
    public function setCategoryPosition($pos);

    /**
     * Get Category Type
     *
     * @return integer
     */
    public function getCategoryType();

    /**
     * Set Category Type
     *
     * @param integer $type
     * @return $this
     */
    public function setCategoryType($type);

    /**
     * Get Category Image
     *
     * @return mixed
     */
    public function getCategoryImage();

    /**
     * Set Category Image
     *
     * @param string $image
     * @return mixed
     */
    public function setCategoryImage($image);

    /**
     * Text Label in Category
     *
     * @return string
     */
    public function getCategoryText();

    /**
     * Set Text label in Category
     *
     * @param string $text
     * @return $this
     */
    public function setCategoryText($text);

    /**
     * Get Css Style in Category
     *
     * @return string
     */
    public function getCssStyleCategory();

    /**
     * Set Css Style in Category
     *
     * @param string $css
     * @return $this
     */
    public function setCssStyleCategory($css);

    /**
     * Display Label in Product List, Product
     *
     * @return boolean
     */
    public function isDisplayInProduct();

    /**
     * Set Display In Product
     *
     * @param boolean $isDisplay
     * @return $this
     */
    public function setDisplayInProduct($isDisplay);

    /**
     * Get Product Position
     *
     * @return string
     */
    public function getProductPosition();

    /**
     * Set Product Position
     *
     * @param $pos
     * @return $this
     */
    public function setProductPosition($pos);


    /**
     * Get Product Type
     *
     * @return integer
     */
    public function getProductType();

    /**
     * Set Product Type
     *
     * @param integer $type
     * @return $this
     */
    public function setProductType($type);

    /**
     * Get Product Image
     *
     * @return mixed
     */
    public function getProductImage();

    /**
     * Set Product Image
     *
     * @param string $image
     * @return mixed
     */
    public function setProductImage($image);

    /**
     * Text Label in Product
     *
     * @return string
     */
    public function getProductText();

    /**
     * Set Text label in Product
     *
     * @param string $text
     * @return $this
     */
    public function setProductText($text);

    /**
     * Get Css Style in Product
     *
     * @return string
     */
    public function getCssStyleProduct();

    /**
     * Set Css Style in Product
     *
     * @param string $css
     * @return $this
     */
    public function setCssStyleProduct($css);
}
