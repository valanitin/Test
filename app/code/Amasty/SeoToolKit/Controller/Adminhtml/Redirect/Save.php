<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_SeoToolKit
 */


declare(strict_types=1);

namespace Amasty\SeoToolKit\Controller\Adminhtml\Redirect;

use Amasty\SeoToolKit\Api\Data\RedirectInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

class Save extends AbstractAction
{
    const MAX_PRIORITY_VALUE = 2147483647;

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function execute()
    {
        $redirectId = (int)$this->getRequest()->getParam(RedirectInterface::REDIRECT_ID);
        $returnToEdit = false;

        try {
            if ($redirectId) {
                $model = $this->redirectRepository->getById($redirectId);
            } else {
                $model = $this->redirectFactory->create();
            }

            $data = $this->getRequest()->getPostValue();
            $model->addData($data);

            $this->prepareData($model);

            if ($this->isWrongAsteriskCount($model)) {
                $this->messageManager->addErrorMessage(__('Wrong count of "*".'));
                $returnToEdit = true;
            } else {
                $this->redirectRepository->save($model);
                $redirectId = $model->getRedirectId();
                $this->messageManager->addSuccessMessage(__('You have saved the Redirect.'));
                $returnToEdit = (bool)$this->getRequest()->getParam('back', false);
            }
        } catch (NoSuchEntityException $exception) {
            $this->messageManager->addErrorMessage(__('This Redirect no longer exists.'));
        } catch (CouldNotSaveException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $returnToEdit = true;
        }

        $resultRedirect = $this->resultRedirectFactory->create();
        if ($returnToEdit && $redirectId) {
            return $resultRedirect->setPath('*/*/edit', [RedirectInterface::REDIRECT_ID => $redirectId]);
        }

        return $resultRedirect->setPath('*/*/');
    }

    private function prepareData(RedirectInterface $redirect)
    {
        if ($redirect->getPriority() > self::MAX_PRIORITY_VALUE) {
            $redirect->setPriority(self::MAX_PRIORITY_VALUE);
        }
        $redirect->setRedirectId($this->getRequest()->getParam(RedirectInterface::REDIRECT_ID) ?: null);
    }

    /**
     * @param RedirectInterface $redirect
     * @return bool
     */
    protected function isWrongAsteriskCount(RedirectInterface $redirect):bool
    {
        $countRequestAsterisk = substr_count($redirect->getRequestPath(), '*');
        $countTargetAsterisk = substr_count($redirect->getTargetPath(), '*');

        return $countRequestAsterisk > 1
            || $countTargetAsterisk > 1
            || ($countTargetAsterisk && $countRequestAsterisk !== $countTargetAsterisk);
    }
}
