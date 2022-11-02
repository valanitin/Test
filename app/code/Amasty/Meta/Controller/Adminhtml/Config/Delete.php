<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Meta
 */


namespace Amasty\Meta\Controller\Adminhtml\Config;

use Amasty\Meta\Api\Data\ConfigInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\NoSuchEntityException;

class Delete extends \Amasty\Meta\Controller\Adminhtml\Custom
{
    /**
     * @var string
     */
    protected $paramName = 'config_id';

    /**
     * Dispatch request
     *
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute()
    {
        $configId = $this->getRequest()->getParam($this->paramName);
        if (!$configId) {
            $this->messageManager->addErrorMessage(__('We can\'t find template to delete.'));
            return $this->resultRedirectFactory->create()->setPath('*/*/');
        }

        try {
            $this->configRepository->deleteById($configId);
            $this->messageManager->addSuccessMessage(__('Template was successfully removed'));
        } catch (NoSuchEntityException $e) {
            $this->messageManager->addErrorMessage(__('This item no longer exists.'));
            $resultRedirect = $this->resultRedirectFactory->create();

            return $resultRedirect->setPath('*/*/');
        } catch (CouldNotDeleteException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(
                __('We can\'t delete item right now. Please review the log and try again.')
            );
            $this->logger->critical($e);
            return $this->resultRedirectFactory->create()->setPath(
                '*/*/edit',
                [$this->paramName => $this->getRequest()->getParam($this->paramName)]
            );
        }

        return $this->resultRedirectFactory->create()->setPath('*/*/');
    }
}
