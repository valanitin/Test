<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Meta
 */


namespace Amasty\Meta\Controller\Adminhtml\Config;

use Amasty\Meta\Api\Data\ConfigInterface;
use Magento\Framework\Exception\NoSuchEntityException;

class Save extends \Amasty\Meta\Controller\Adminhtml\Config
{
    /**
     * @var string
     */
    protected $paramName = 'config_id';

    public function execute()
    {
        $data  = $this->getRequest()->getPostValue();
        if ($data) {
            try {
                $id = $this->getRequest()->getParam($this->paramName);
                if ($id) {
                    $model = $this->configRepository->getById($id);
                } else {
                    $model = $this->configFactory->create();
                }

                $model->addData($data);
                $this->configRepository->save($model);

                $msg = __('%1 has been successfully saved', $this->_title);
                $this->messageManager->addSuccessMessage($msg);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', [$this->paramName => $model->getId()]);
                    return;
                } else {
                    $this->_redirect('*/*');
                }
            } catch (NoSuchEntityException $exception) {
                $this->messageManager->addErrorMessage(__('This Template no longer exists.'));
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                $this->logger->critical($e);
                $this->_redirect('*/*/edit', [$this->paramName => $id]);
            }
            return;
        }

        $this->messageManager->addErrorMessage(__('Unable to find a record to save'));
        $this->_redirect('*/*');
    }
}
