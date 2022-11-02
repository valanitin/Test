<?php
namespace Magentizer\Erpservices\Model;
use Magentizer\Erpservices\Api\Getorderstatus;
class GetorderstatusModel implements Getorderstatus {
	protected $statusCollectionFactory;
	public function __construct(
        \Magento\Sales\Model\ResourceModel\Order\Status\CollectionFactory $statusCollectionFactory
	) {
        $this->statusCollectionFactory = $statusCollectionFactory;
	}
	public function getorderstatusarray() {
    	$options = $this->statusCollectionFactory->create()->toOptionArray();
    	return $options;
	}
}