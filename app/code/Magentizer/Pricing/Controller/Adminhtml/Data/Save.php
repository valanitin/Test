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

use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Magento\Backend\Model\View\Result\ForwardFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Message\Manager;
use Magento\Framework\Api\DataObjectHelper;
use Magentizer\Pricing\Api\DataRepositoryInterface;
use Magentizer\Pricing\Api\Data\DataInterface;
use Magentizer\Pricing\Api\Data\DataInterfaceFactory;
use Magentizer\Pricing\Controller\Adminhtml\Data;

class Save extends Data
{
    /**
     * @var Manager
     */
    protected $messageManager;

    /**
     * @var DataRepositoryInterface
     */
    protected $dataRepository;

    /**
     * @var DataInterfaceFactory
     */
    protected $dataFactory;

    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;

    public function __construct(Registry $registry, DataRepositoryInterface $dataRepository,
        PageFactory $resultPageFactory, ForwardFactory $resultForwardFactory, Manager $messageManager,
        DataInterfaceFactory $dataFactory, DataObjectHelper $dataObjectHelper, \Magentizer\Pricing\Cron\FetchPrices
        $cronFile, Context $context)
    {
        $this->messageManager = $messageManager;
        $this->dataFactory = $dataFactory;
        $this->dataRepository = $dataRepository;
        $this->dataObjectHelper = $dataObjectHelper;
        parent::__construct($registry, $dataRepository, $resultPageFactory, $resultForwardFactory,
            $cronFile, $context);
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();

        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            $id = $this->getRequest()->getParam('pricing_id');
            if ($id) {
                $model = $this->dataRepository->getById($id);
            } else {
                unset($data['pricing_id']);
                $model = $this->dataFactory->create();
            }

            try {

                //echo "<pre>";
                //print_r($data);
                $this->dataObjectHelper->populateWithArray($model, $data, DataInterface::class);
                $this->dataRepository->save($model);

                //print_r($this->dataRepository->getById(1)->getData());exit;
                $this->messageManager->addSuccessMessage(__('You saved this data.'));
                $this->_getSession()->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['pricing_id' => $model->getId(),
                        '_current' => true]);
                }
                return $resultRedirect->setPath('*/*/');
            }
            catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }
            catch (\RuntimeException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }
            catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the data.'));
            }

            $this->_getSession()->setFormData($data);
            return $resultRedirect->setPath('*/*/edit', ['pricing_id' => $this->getRequest()->
                getParam('pricing_id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }
}
