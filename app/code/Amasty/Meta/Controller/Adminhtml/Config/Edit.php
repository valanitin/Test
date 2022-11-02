<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Meta
 */


namespace Amasty\Meta\Controller\Adminhtml\Config;

use Amasty\Meta\Api\Data\ConfigInterface;
use Magento\Framework\Exception\NoSuchEntityException;

class Edit extends \Amasty\Meta\Controller\Adminhtml\Config
{
    /**
     * @var string
     */
    protected $paramName = 'config_id';

    public function execute()
    {
        $id = (int)$this->getRequest()->getParam($this->paramName);

        try {
            if ($id) {
                $model = $this->configRepository->getById($id);
            } else {
                $model = $this->configFactory->create();
            }
        } catch (NoSuchEntityException $e) {
            $this->messageManager->addErrorMessage(__('This item no longer exists.'));
            $resultRedirect = $this->resultRedirectFactory->create();

            return $resultRedirect->setPath('*/*/');
        }

        $this->registry->register('ammeta_config', $model);
        $this->_view->loadLayout();
        $this->_setActiveMenu('cms/amseotoolkit/ammeta');

        if ($model->getConfigId()) {
            $title = __('Edit Template #`%1`', $model->getConfigId());
        } else {
            $title = __("Add New");
        }

        $this->_view->getPage()->getConfig()->getTitle()->prepend($title);
        $this->_view->renderLayout();
    }
}
