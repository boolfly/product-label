<?php
/************************************************************
 * *
 *  * Copyright © Boolfly. All rights reserved.
 *  * See COPYING.txt for license details.
 *  *
 *  * @author    info@boolfly.com
 * *  @project   Product Label
 */
namespace Boolfly\ProductLabel\Model\Source\Config;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;
use Magento\Eav\Model\Entity\Attribute\Source\SourceInterface;
use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Type
 *
 * @package Boolfly\ProductLabel\Model\Source\Config
 */
class Type extends AbstractSource implements SourceInterface, OptionSourceInterface
{
    /**#@+
     * Type values
     */
    const NEW_LABEL = 1;

    const SALE_LABEL = 2;

    const BEST_SELLER_LABEL = 3;

    const CUSTOM_LABEL = 10;


    /**#@-*/

    /**
     * Retrieve option array
     *
     * @return string[]
     */
    public static function getOptionArray()
    {
        return [
            self::NEW_LABEL => __('New'),
            self::SALE_LABEL => __('Sale'),
            self::BEST_SELLER_LABEL => __('Best Seller'),
            self::CUSTOM_LABEL => __('Custom')
        ];
    }

    /**
     * Retrieve option array with empty value
     *
     * @return string[]
     */
    public function getAllOptions()
    {
        $result = [];

        foreach (self::getOptionArray() as $index => $value) {
            $result[] = ['value' => $index, 'label' => $value];
        }

        return $result;
    }
}
