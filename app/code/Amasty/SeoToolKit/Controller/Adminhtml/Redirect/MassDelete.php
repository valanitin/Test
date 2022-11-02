<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_SeoToolKit
 */


declare(strict_types=1);

namespace Amasty\SeoToolKit\Controller\Adminhtml\Redirect;

use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Exception\CouldNotDeleteException;

class MassDelete extends MassActionAbstract
{
    /**
     * @param AbstractDb $collection
     */
    protected function doAction(AbstractDb $collection)
    {
        $notDeletedItems = 0;
        $collectionSize = $collection->getSize();
        foreach ($collection as $redirect) {
            try {
                $this->redirectRepository->delete($redirect);
            } catch (CouldNotDeleteException $e) {
                $notDeletedItems++;
                $this->logger->error($e->getMessage());
            }
        }
        if ($notDeletedItems) {
            $this->messageManager->addSuccessMessage(
                __('A total of %1 record(s) have not been deleted.', $notDeletedItems)
            );
        }

        $this->messageManager->addSuccessMessage(
            __('A total of %1 record(s) have been deleted.', abs($collectionSize - $notDeletedItems))
        );
    }
}
