<?php

namespace Dynamic\Referfriend\Controller\Index;

class Post extends \Magento\Framework\App\Action\Action
{   
    /**
     * Store manager
     *
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * Referfriend
     *
     * @var \Dynamic\Referfriend\Model\Referfriend
     */
    protected $referfriend;

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Action\Context  $context
     * @param \Dynamic\Referfriend\Model\Referfriend $referfriend
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Dynamic\Referfriend\Model\Referfriend $referfriend,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    ) { 
        $this->referfriend    = $referfriend;
        $this->resultJsonFactory = $resultJsonFactory;
        parent::__construct($context);
    }

    /**
     * Execute view action
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $data = $this->getRequest()->getParams();

        $response = [];

        if($data){
            
            $this->referfriend->setData($data);
            try {
                $this->referfriend->save();
                $this->messageManager->addSuccessMessage(__('Request Successfully Sent')); 
            
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }
        }
        return $this->resultRedirectFactory->create()->setPath('referfriend/index/success');
    }
}

