<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_SeoToolKit
 */


declare(strict_types=1);

namespace Amasty\SeoToolKit\Controller\Adminhtml\Redirect;

use Amasty\SeoToolKit\Api\Data\RedirectInterface;
use Magento\Framework\Exception\CouldNotDeleteException;

class Delete extends AbstractAction
{
    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $redirectId = $this->getRequest()->getParam(RedirectInterface::REDIRECT_ID);
        if (!$redirectId) {
            $this->messageManager->addErrorMessage(__('We can\'t find redirect to delete.'));
            return $this->resultRedirectFactory->create()->setPath('*/*/');
        }

        try {
            $this->redirectRepository->deleteById($redirectId);
            $this->messageManager->addSuccessMessage(__('Redirect was successfully removed'));
        } catch (CouldNotDeleteException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        return $this->resultRedirectFactory->create()->setPath('*/*/');
    }
}
