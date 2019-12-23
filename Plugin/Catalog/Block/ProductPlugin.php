<?php
/************************************************************
 * *
 *  * Copyright © Boolfly. All rights reserved.
 *  * See COPYING.txt for license details.
 *  *
 *  * @author    info@boolfly.com
 * *  @project   Product Label
 */
namespace Boolfly\ProductLabel\Plugin\Catalog\Block;

use Magento\Catalog\Block\Product\AbstractProduct;
use Magento\Catalog\Block\Product\Image;
use Magento\Catalog\Model\Product;
use Boolfly\ProductLabel\Model\Config;

/**
 * Class ProductPlugin
 *
 * @package Boolfly\ProductLabel\Plugin\Catalog\Block\Product
 * @see \Magento\Catalog\Block\Product\AbstractProduct
 */
class ProductPlugin
{
    /**
     * @var Config
     */
    private $config;

    /**
     * ProductPlugin constructor.
     *
     * @param Config $config
     */
    public function __construct(
        Config $config
    ) {
        $this->config = $config;
    }

    /**
     * Set Product
     *
     * @param $subject
     * @param $result
     * @param Image   $product
     * @param $imageId
     * @param array   $attribute
     * @return mixed
     */
    public function afterGetImage(
        AbstractProduct $subject,
        $result,
        $product,
        $imageId,
        $attribute = []
    ) {
        if ($result instanceof Image && $this->config->isEnable()) {
            $result->setData('product', $product);
        }

        return $result;
    }
}
