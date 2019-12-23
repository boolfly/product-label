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

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Position
 *
 * @package Boolfly\ProductLabel\Model\Source\Config
 */
class Position implements OptionSourceInterface
{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = [
            ['value' => 'top-left', 'label' => __('Top-Left')],
            ['value' => 'top-right', 'label' => __('Top-Right')],
            ['value' => 'bot-left', 'label' => __('Bottom-Left')],
            ['value' => 'bot-right', 'label' => __('Bottom-Right')]
        ];

        return $options;
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'top-left' => __('Top-Left'),
            'top-right' => __('Top-Right'),
            'bottom-left' => __('Bottom-Left'),
            'bottom-right' => __('Bottom-Right')
        ];
    }
}
