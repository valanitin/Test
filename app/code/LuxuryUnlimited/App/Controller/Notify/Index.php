<?php

namespace LuxuryUnlimited\App\Controller\Notify;

use Magento\Framework\App\Action\Context;
use \Magento\Framework\Controller\ResultFactory;
use \Magento\Store\Model\ScopeInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Store\Model\StoreManagerInterface;
use LuxuryUnlimited\App\Helper\Sender;

class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var JsonFactory
     */
    protected $jsonFactory;

    /**
     * @var Sender
     */
    protected $Sender;

    /**
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     * @param JsonFactory $jsonFactory
     * @param Sender $sender
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        JsonFactory $jsonFactory,
        Sender $sender

    ) {

        $this->storeManager =  $storeManager;
        $this->jsonFactory = $jsonFactory;
        $this->sender = $sender;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        // $post = (array) $this->getRequest()->getPost();
        $post = (array) $this->getRequest()->getParams();

        /** @var \Magento\Framework\Controller\Result\Raw $resultRaw */
        $resultJson = $this->jsonFactory->create();
        $app_link = $this->sender->getConfig('appdownload/general/link');

        $post['app_link'] = $app_link ;

        $response = __('Something went wrong, plese try again.');

        if(isset($post['email']) && isset($post['app_link'])){
            $sent = $this->sender->notifyClient($post);
            if(!$sent){
                $response = __('Email could be sent, please try again later.');
            } else {
                $response = __("Email sent successfully");
            }
        }


      return $resultJson->setData($response);
    }

    /**
     * @return int
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getStoreId()
    {
        return $this->storeManager->getStore()->getId();
    }

}
