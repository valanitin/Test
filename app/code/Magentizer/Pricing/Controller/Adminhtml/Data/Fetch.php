<?php
/*
 * Magentizer_Pricing

 * @category   SussexDev
 * @package    Magentizer_Pricing
 * @copyright  Copyright (c) 2019 Scott Parsons
 * @license    https://github.com/ScottParsons/module-sampleuicomponent/blob/master/LICENSE.md
 * @version    1.1.2
 */
namespace Magentizer\Pricing\Controller\Adminhtml\Data;

use Magentizer\Pricing\Controller\Adminhtml\Data;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

class Fetch extends Data
{
    /**
     * Delete the data entity
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
       
            try {
                $this->cronFile->getPrices();
                $this->messageManager->addSuccessMessage(__('Prices Has Been Fetched Successfully.'));
                $resultRedirect->setPath('erppricing/data/index');
                return $resultRedirect;
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                return $resultRedirect->setPath('erppricing/data/index');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__('There was a problem Fetching the data ' . $e->getMessage()));
                return $resultRedirect->setPath('erppricing/data/index');
            }

        $this->messageManager->addErrorMessage(__('There was a problem Fetching the data.'));
        $resultRedirect->setPath('erppricing/data/index');
        return $resultRedirect;
    }
}
