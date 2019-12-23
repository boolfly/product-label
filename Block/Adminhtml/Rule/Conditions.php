<?php
/************************************************************
 * *
 *  * Copyright © Boolfly. All rights reserved.
 *  * See COPYING.txt for license details.
 *  *
 *  * @author    info@boolfly.com
 * *  @project   Product Label
 */
namespace Boolfly\ProductLabel\Block\Adminhtml\Rule;

use Boolfly\ProductLabel\Model\Rule;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form as WidgetForm;
use Magento\Backend\Block\Widget\Form\Renderer\Fieldset;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;
use Magento\Rule\Block\Conditions as RuleConditions;
use Magento\Rule\Model\Condition\AbstractCondition;

/**
 * Class Conditions
 *
 * @package Boolfly\ProductLabel\Block\Adminhtml\Rule
 */
class Conditions extends WidgetForm
{
    /**
     * @var Fieldset
     */
    protected $rendererFieldset;

    /**
     * @var RuleConditions
     */
    protected $conditions;

    /**
     * @var Registry
     */
    private $coreRegistry;

    /**
     * @var string
     */
    private $formName = 'boolfly_label_rule_form';

    /**
     * @var string
     */
    private $rendererFieldsetTemplate = 'Boolfly_ProductLabel::rule/fieldset.phtml';

    /**
     * @var FormFactory
     */
    private $formFactory;

    /**
     * Conditions constructor.
     *
     * @param Context        $context
     * @param Registry       $registry
     * @param FormFactory    $formFactory
     * @param RuleConditions $conditions
     * @param Fieldset       $rendererFieldset
     * @param array          $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        RuleConditions $conditions,
        Fieldset $rendererFieldset,
        array $data = []
    ) {
        $this->coreRegistry     = $registry;
        $this->rendererFieldset = $rendererFieldset;
        $this->conditions       = $conditions;
        parent::__construct($context, $data);
        $this->formFactory = $formFactory;
    }

    /**
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareForm()
    {
        $model = $this->coreRegistry->registry('current_label_rule');
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->addTabToForm($model);
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * @param Rule   $model
     * @param string $fieldSetId
     * @return \Magento\Framework\Data\Form
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function addTabToForm($model, $fieldSetId = 'conditions_fieldset')
    {
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->formFactory->create();
        $form->setHtmlIdPrefix('rule_');
        $renderer = $this->rendererFieldset->setTemplate($this->rendererFieldsetTemplate)
            ->setNewChildUrl($this->getNewChildUrl($model))
            ->setFieldSetId($model->getConditionsFieldSetId($this->formName));

        $fieldset = $form->addFieldset(
            $fieldSetId,
            ['legend' => __('Conditions (don\'t add conditions if rule is applied to all products)')]
        )->setRenderer($renderer);

        $fieldset->addField(
            'conditions',
            'text',
            [
                'name' => 'conditions',
                'label' => __('Conditions'),
                'title' => __('Conditions'),
                'required' => true,
                'data-form-part' => $this->formName
            ]
        )->setRule($model)->setRenderer($this->conditions);

        $form->setValues($model->getData());
        $this->setConditionFormName($model->getConditions(), $this->formName);
        return $form;
    }

    /**
     * @param Rule $model
     * @return string
     */
    private function getNewChildUrl($model)
    {
        return $this->getUrl(
            '*/*/newConditionHtml',
            [
                'form' => $model->getConditionsFieldSetId($this->formName),
                'form_namespace' => $this->formName
            ]
        );
    }

    /**
     * @param AbstractCondition $conditions
     * @param $formName
     */
    private function setConditionFormName(AbstractCondition $conditions, $formName)
    {
        $conditions->setFormName($formName);
        $conditions->setJsFormObject($formName);
        if ($conditions->getConditions() && is_array($conditions->getConditions())) {
            foreach ($conditions->getConditions() as $condition) {
                $this->setConditionFormName($condition, $formName);
            }
        }
    }
}
