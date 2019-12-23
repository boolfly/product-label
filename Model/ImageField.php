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

/**
 * Class ImageField
 *
 * @package Boolfly\ProductLabel\Model
 */
class ImageField
{

    /**
     * Return Image Field
     *
     * @return array
     */
    public static function getField()
    {
        return [
            'cat_image',
            'product_image'
        ];
    }
}
