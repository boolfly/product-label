<?php
/************************************************************
 * *
 *  * Copyright © Boolfly. All rights reserved.
 *  * See COPYING.txt for license details.
 *  *
 *  * @author    info@boolfly.com
 * *  @project   Product Label
 */
namespace Boolfly\ProductLabel\Model\Rule;

use Boolfly\Base\Model\ImageUploader;
use Boolfly\ProductLabel\Model\ResourceModel\Rule\CollectionFactory;
use Boolfly\ProductLabel\Model\ResourceModel\Rule\Collection;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Magento\Framework\Registry;
use Boolfly\ProductLabel\Api\Data\RuleInterface;
use Boolfly\ProductLabel\Model\ImageField;

/**
 * Class DataProvider
 *
 * @package Boolfly\ProductLabel\Model\Rule
 */
class DataProvider extends AbstractDataProvider
{

    /**
     * @var Collection
     */
    protected $collection;

    /**
     * @var array
     */
    protected $loadedData;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * @var ImageUploader
     */
    private $imageUploader;

    /**
     * DataProvider constructor.
     *
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param ImageUploader     $imageUploader
     * @param Registry          $registry
     * @param array             $meta
     * @param array             $data
     */
    public function __construct(
        string $name,
        string $primaryFieldName,
        string $requestFieldName,
        CollectionFactory $collectionFactory,
        ImageUploader $imageUploader,
        Registry $registry,
        array $meta = [],
        array $data = []
    ) {
        $this->collection   = $collectionFactory->create();
        $this->coreRegistry = $registry;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->imageUploader = $imageUploader;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        /** @var RuleInterface | \Boolfly\ProductLabel\Model\Rule $model */
        $model = $this->coreRegistry->registry('current_label_rule');
        if ($model->getId()) {
            $data = $model->getData();
            foreach (ImageField::getField() as $field) {
                unset($data[$field]);
                $imageName = $model->getData($field);
                if ($imageSrc = $this->imageUploader->getImageUrl($imageName)) {
                    try {
                        $size = $this->imageUploader->getSize($imageName);
                    } catch (\Exception $e) {
                        $size = 'undefined';
                    }
                    $data[$field][] = [
                        'name' => $imageName,
                        'url' => $imageSrc,
                        'previewType' => 'image',
                        'size' => $size
                    ];
                } else {
                    $data[$field] = [];
                }
            }
            $this->loadedData[$model->getId()] = $data;
        } else {
            $this->loadedData = [];
        }

        return $this->loadedData;
    }
}
