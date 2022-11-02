<?php

namespace Dynamic\Customization\Controller\Index;

class Cancelorder extends \Magento\Framework\App\Action\Action
{
	/**
	 * Store manager
	 *
	 * @var \Magento\Framework\Controller\Result\JsonFactory
	 */
	protected $resultJsonFactory;

	protected $orderManagement;


	/**
	 * Http
	 *
	 * @var \Magento\Framework\App\Http\Context
	 */
	protected $http;

	/**
	 * Constructor
	 *
	 * @param \Magento\Framework\App\Action\Context  $context
	 * @param \Dynamic\Notifyme\Model\Notifyme $notifyme
	 * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
	 * @param \Magento\Customer\Model\Session $customerSession
	 * @param \Magento\Framework\App\Http\Context $http
	 */
	public function __construct(
		\Magento\Framework\App\Action\Context $context,
		\Magento\Sales\Api\OrderManagementInterface $orderManagement,
		\Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
		\Magento\Framework\App\Http\Context $http
	) {
		$this->orderManagement = $orderManagement;
		$this->resultJsonFactory = $resultJsonFactory;
		$this->http = $http;
		parent::__construct($context);
	}

	/**
	 * Execute view action
	 *
	 * @return \Magento\Framework\Controller\Result\JsonFactory
	 */
	public function execute()
	{
		$data = $this->getRequest()->getParams();
		
		$response = [];

		if ($data) {
			try {
			    $res = $this->cancelOrder($data["order_cancel_id"]);
				$response = [
                    'errors' => false,
                    'message' => __($res)
                ];
			} catch (\Exception $e) {
			    $response = [
			        'errors' => true,
			        'message' => $e->getMessage()
			    ];
			}
		}

		$resultJson = $this->resultJsonFactory->create();
		return $resultJson->setData($response);
	}

	/**
	 * int $orderId
	 * Order cancel by order id $orderId 
	 */
	public function cancelOrder($orderId)
	{
		try {
			$this->orderManagement->cancel($orderId);
			return __('Cancellation request sent successfully.');
		} catch (\Exception $e) {
			return __('You have not canceled the order.');
		}
	}
}
