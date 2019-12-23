<?php
/************************************************************
 * *
 *  * Copyright © Boolfly. All rights reserved.
 *  * See COPYING.txt for license details.
 *  *
 *  * @author    info@boolfly.com
 * *  @project   Product Label
 */
namespace Boolfly\ProductLabel\Controller\Adminhtml\Image;

use Boolfly\Base\Controller\Adminhtml\Image\AbstractUpload;

/**
 * Class Upload
 *
 * @package Boolfly\ProductLabel\Controller\Adminhtml\Image
 */
class Upload extends AbstractUpload
{
    /**
     *  Check admin permissions for this controller
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Boolfly_ProductLabel::rule';
}
