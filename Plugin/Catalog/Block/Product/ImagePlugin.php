<?php
/************************************************************
 * *
 *  * Copyright © Boolfly. All rights reserved.
 *  * See COPYING.txt for license details.
 *  *
 *  * @author    info@boolfly.com
 * *  @project   Product Label
 */
namespace Boolfly\ProductLabel\Plugin\Catalog\Block\Product;

use Boolfly\ProductLabel\Block\Label;
use Boolfly\ProductLabel\Model\Config;
use Magento\Catalog\Block\Product\Image;
use Magento\Framework\View\LayoutInterface;

/**
 * Class ImagePlugin
 *
 * @package Boolfly\ProductLabel\Plugin\Catalog\Block\Product
 * @see \Magento\Catalog\Block\Product\Image
 */
class ImagePlugin
{
    /**
     * @var LayoutInterface
     */
    private $layout;

    /**
     * @var Config
     */
    private $config;

    /**
     * ImagePlugin constructor.
     *
     * @param Config          $config
     * @param LayoutInterface $layout
     */
    public function __construct(
        Config $config,
        LayoutInterface $layout
    ) {
        $this->layout = $layout;
        $this->config = $config;
    }

    /**
     * Add Label Html after Product Image
     *
     * @param Image  $image
     * @param string $result
     * @return string
     */
    public function afterToHtml(
        Image $image,
        $result
    ) {
        if ($image->getData('product') && $result && $this->config->isEnable()) {
            /** @var Label $labelBlock */
            $labelBlock = $this->layout->createBlock(Label::class);
            $labelBlock->setProduct($image->getData('product'));
            $labelHtml = $labelBlock->toHtml();
            if ($labelHtml) {
                return $this->wrapper($result, $labelHtml);
            }
        }
        return $result;
    }

    /**
     * Wrapper Html
     *
     * @param $result
     * @param $labelHtml
     * @return string
     */
    protected function wrapper($result, $labelHtml)
    {
        return rtrim(trim($result), '</span>') . $labelHtml . '</span>';
    }
}
