<?php
declare(strict_types=1);

namespace Dynamic\Returnstatus\Model;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\DataObject;
use Dynamic\Returnstatus\Api\ReturnstatusManagementInterface;

/**
 * Class ReturnstatusManagement
 *
 */
class ReturnstatusManagement implements ReturnstatusManagementInterface
{ 
    protected $dataObjectFactory;
    
    protected $request;

    protected $orderreturn;

    public function __construct(
        \Magento\Framework\DataObjectFactory $dataObjectFactory,
        \Magento\Framework\App\RequestInterface $request,
        \Dynamic\Returnstatus\Model\Returnstatus $orderreturn,
        \Dynamic\Returnstatus\Model\ResourceModel\Returnstatus\Collection $orderreturnCollection
    ) {
        $this->dataObjectFactory = $dataObjectFactory;
        $this->request = $request;
        $this->orderreturn = $orderreturn;
        $this->orderreturnCollection = $orderreturnCollection;
    }

    /**
    * @inheritDoc
    */
    public function submitForm($status) {
        $result = $this->dataObjectFactory->create();

        $response = [];

        if (empty($status)) {
            $result->setData('message', 'Please enter required fields.');
            return $result;
        }

        $applied = $this->orderreturn->getCollection()->addfieldtofilter('status', strtolower($status));
        if($status && $applied->getSize() <= 0) {
            $this->orderreturn->setData('status',strtolower($status));
            try {
                $this->orderreturn->save();
                $result->setData('message', 'Return Status has been saved');
                return $result;

            } catch (\Exception $e) {
                $result->setData('message', $e->getMessage());
                return $result;
            }
        } else {
            $result->setData('message', 'Return Status has already saved.');
            return $result;
        }
    }
    public function getList()
    {
        if (empty($this->orderreturnCollection->getData())) {
            $data = array(
                [   'status' => 'No Data',
                    'message' => __('There are no return status in this website') 
                ]
            );
            return $data;
        }
        return $this->orderreturnCollection->getData();
    }
}