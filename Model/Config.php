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

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Config
 *
 * @package Boolfly\ProductLabel\Model
 */
class Config
{
    /**@#%
     * @const
     */
    const XML_PATH_PRODUCT_LABEL_ENABLE = 'product_label/settings/enable';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * Config constructor.
     *
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Get Configuration
     *
     * @param $path
     * @return mixed
     */
    public function getConfig($path)
    {
        return $this->scopeConfig->getValue($path, ScopeInterface::SCOPE_STORES);
    }

    /**
     * Enable Mega menu
     *
     * @return boolean
     */
    public function isEnable()
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_PRODUCT_LABEL_ENABLE, ScopeInterface::SCOPE_STORES);
    }
}
