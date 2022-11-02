<?php

namespace Dynamic\Orderreturn\Model;

use Dynamic\Orderreturn\Api\OrderreturnlistManagementInterface;

class OrderreturnlistManagement implements OrderreturnlistManagementInterface
{
    /**
     * @var \Dynamic\Orderreturn\Model\Orderreturn
     */
    protected $orderReturnCollection;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $http;

    /**
     * @var \Magento\Integration\Model\Oauth\TokenFactory
     */
    protected $tokenFactory;

    /**
     * @var \Magento\Customer\Model\Customer
     */
    protected $customer;

    /**
     * Order Return list data constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Dynamic\Orderreturn\Model\Orderreturn $orderReturnCollection
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Framework\App\Request\Http $http
     * @param \Magento\Integration\Model\Oauth\TokenFactory $tokenFactory
     * @param \Magento\Customer\Model\Customer $customer
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Dynamic\Orderreturn\Model\Orderreturn $orderReturnCollection,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\App\Request\Http $http,
        \Magento\Integration\Model\Oauth\TokenFactory $tokenFactory,
        \Magento\Customer\Model\Customer $customer
    ) {
        $this->orderReturnCollection = $orderReturnCollection;
        $this->request = $request;
        $this->http = $http;
        $this->tokenFactory = $tokenFactory;
        $this->customer = $customer;
    }

    /**
     * Order Return list data
     *
     * @api
     * @return Order Return array collection.
     */
    public function getOrderReturnList()
    {

        $authorizationHeader = $this->http->getHeader('Authorization');

        $tokenParts = explode('Bearer', $authorizationHeader);
        $tokenPayload = trim(array_pop($tokenParts));

        if(!$tokenPayload) {
            $data = [
                ['status' => 'No Data','message' => __('Please enter the customer bearer token.') ]
            ];
            return $data;
        }

        /** @var Token $token */
        $token = $this->tokenFactory->create();
        $token->loadByToken($tokenPayload);
        $customerId = $token->getCustomerId();
        $customer = $this->customer->load($customerId);
        $email = $customer->getEmail();

        $data = [];

        if($email) {
            $orderReturnCollection = $this->orderReturnCollection->getCollection();
            $orderReturnCollection->setOrder('orderreturn_id ','DESC');
            $orderReturnCollection->addFieldToFilter('customer_email', ['eq' => $email]);

            if(!empty($orderReturnCollection) && count($orderReturnCollection) > 0) {
                foreach ($orderReturnCollection as $collection) {
                    
                    $data[] = [
                        "orderreturn_id" => $collection->getOrderreturnId(),
                        "order_id" => $collection->getOrderId(),
                        "product_sku" => $collection->getProductSku(),
                        "customer_email" => $collection->getCustomerEmail(),
                        "type" => $collection->getReturn(),
                        "reason" => $collection->getReason(),
                        "lang_code" => $collection->getLangCode(),
                        "website" => $collection->getWebsite(),
                        "erp_return_status" => $collection->getErpReturnStatus(),
                        "created_at" => $collection->getCreatedAt()
                    ];
                }
            } else {
                $data = [
                    ['status' => 'No Data','message' => __('There are no Order Return data in this website.') ]
                ];
            }
        } else {
            $data = [
                ['status' => 'No Data','message' => __('Please enter the email id.') ]
            ];
        }

        return $data;
    }
}
