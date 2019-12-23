<?php
/************************************************************
 * *
 *  * Copyright © Boolfly. All rights reserved.
 *  * See COPYING.txt for license details.
 *  *
 *  * @author    info@boolfly.com
 * *  @project   Product Label
 */
namespace Boolfly\ProductLabel\Controller\Adminhtml\Rule;

use Boolfly\ProductLabel\Controller\Adminhtml\AbstractRule;
use Boolfly\ProductLabel\Model\RuleFactory;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\Session;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Psr\Log\LoggerInterface;

/**
 * Class Save
 *
 * @package Boolfly\ProductLabel\Controller\Adminhtml\Rule
 */
class Save extends AbstractRule
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var Session
     */
    private $backendSession;

    /**
     * Save constructor.
     *
     * @param Context         $context
     * @param Registry        $coreRegistry
     * @param Session         $backendSession
     * @param LoggerInterface $logger
     * @param RuleFactory     $ruleFactory
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        Session $backendSession,
        LoggerInterface $logger,
        RuleFactory $ruleFactory
    ) {
        parent::__construct($context, $coreRegistry, $ruleFactory);
        $this->logger         = $logger;
        $this->backendSession = $backendSession;
    }

    /**
     * @return mixed
     * @throws LocalizedException
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            /** @var \Boolfly\ProductLabel\Model\Rule $model */
            $model = $this->ruleFactory->create();

            if (!empty($data['rule_id'])) {
                $model->load($data['rule_id']);
                if (!$model->getId()) {
                    throw new LocalizedException(__('Wrong Label Rule ID.'));
                }
            }
            unset($data['rule_id']);

            if (isset($data['rule']['conditions'])) {
                $data['conditions'] = $data['rule']['conditions'];
                unset($data['rule']);
            }
            $model->loadPost($data);
            $this->_objectManager->get('Magento\Backend\Model\Session')->setPageData($model->getData());
            try {
                $this->_eventManager->dispatch(
                    'controller_boolfly_label_rule_save_entity_before',
                    ['controller' => $this, 'label_rule' => $model]
                );
                $model->save();
                $this->messageManager->addSuccessMessage(__('The rule has been saved.'));
                $this->backendSession->setPageData(false);
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['id' => $model->getId(), '_current' => true]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->logger->critical($e);
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                echo $e->getMessage();
                $this->messageManager->addErrorMessage($e, __('Something went wrong while saving the rule.'));
                $this->logger->critical($e);
                $this->backendSession->setPageData($data);
                return $resultRedirect->setPath('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
            }
        }

        return $resultRedirect->setPath('*/*/');
    }
}
