<?php 

namespace Dynamic\Orderreturn\Model;

class ReturnConfig extends \Magento\Framework\Model\AbstractModel {
	
	protected $orderreturnCollection;

	public function __construct(
	    \Dynamic\Returnstatus\Model\ResourceModel\Returnstatus\Collection $orderreturnCollection
	) {
	    $this->orderreturnCollection = $orderreturnCollection;
	}

	public function getReturnStatusArray() {

		$status = [];

		if(!empty($this->orderreturnCollection->getData())) {
			foreach($this->orderreturnCollection as $orderReturn) {
		        $status[strtolower($orderReturn->getStatus())] = $orderReturn->getStatus();
		    }
		}
	    
	    return $status;
	}

}
