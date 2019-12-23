<?php
/************************************************************
 * *
 *  * Copyright © Boolfly. All rights reserved.
 *  * See COPYING.txt for license details.
 *  *
 *  * @author    info@boolfly.com
 * *  @project   Product Label
 */
namespace Boolfly\ProductLabel\Observer;

use Boolfly\ProductLabel\Api\Data\RuleInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Boolfly\Base\Model\ImageUploader;
use Boolfly\ProductLabel\Model\ImageField;

/**
 * Class ProcessingImageUpload
 *
 * @package Boolfly\ProductLabel\Observer
 *
 * @event controller_boolfly_label_rule_save_entity_before
 */
class ProcessingImageUpload implements ObserverInterface
{
    /**
     * @var ImageUploader
     */
    private $imageUploader;

    /**
     * ProcessingImageUpload constructor.
     *
     * @param ImageUploader $imageUploader
     */
    public function __construct(
        ImageUploader $imageUploader
    ) {
        $this->imageUploader = $imageUploader;
    }

    /**
     * @param Observer $observer
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(Observer $observer)
    {
        $rule = $observer->getEvent()->getData('label_rule');
        if ($rule instanceof RuleInterface) {
            foreach (ImageField::getField() as $field) {
                $this->processFile($rule, $field);
            }
        }
    }

    /**
     * Process File
     *
     * @param RuleInterface|\Magento\Framework\DataObject $object
     * @param $key
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function processFile(RuleInterface $object, $key)
    {
        $files = $object->getData($key);
        $object->setData($key, null);
        if (is_array($files) && !empty($files)) {
            foreach ($files as $file) {
                if (is_array($file) && empty($file['name'])) {
                    continue;
                }
                $name = $file['name'];
                // Upload New File
                if (isset($file['type']) && $file['tmp_name']) {
                    $this->imageUploader->moveFileFromTmp($name);
                }
                $object->setData($key, $name);
            }
        }

        return $this;
    }
}
