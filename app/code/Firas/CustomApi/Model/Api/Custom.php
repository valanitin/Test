<?php
namespace Firas\CustomApi\Model\Api;

use Psr\Log\LoggerInterface;
use Magento\Framework\Json\Helper\Data;


class Custom
{
    protected $logger;

    /**
     * @var \Firas\Grid\Model\GridFactory
     */
    var $gridFactory;

    /**
     * @param \Firas\Grid\Model\GridFactory $gridFactory
     */

    /**
     * @var jsonHelper
     */
    protected $jsonHelper;

    /**
     * @param Data $jsonHelper
     */

    public function __construct(
        LoggerInterface $logger,
        Data $jsonHelper,
        \Firas\Grid\Model\GridFactory $gridFactory,
        \Magento\Framework\App\ResourceConnection $resource
        )
    {

        $this->logger = $logger;
        $this->jsonHelper = $jsonHelper;
        $this->gridFactory = $gridFactory;
        $this->resource = $resource;

    }

    /**
     * @inheritdoc
     */

    public function getPost($shippingCountryCode, $shippingCountryName, $shippingPrice, $shippingCurrency)
    {
    	$response = array();
        try
        {

            if(!empty($shippingCountryCode)){
               //save data in shipping price table
                $shippingData = $this->gridFactory->create();
                $shipCollection = $shippingData->getCollection()->addFieldToFilter("shipping_country_code", $shippingCountryCode);
                $shipCount = count($shipCollection);
                if($shipCount <= 0){
                    $shippingData->setShippingCountryCode($shippingCountryCode);
                    $shippingData->setShippingCountryName($shippingCountryName);
                    $shippingData->setShippingPrice($shippingPrice);
                    $shippingData->setShippingPriceCurrency($shippingCurrency);
                    $shippingData->save();
                    $shipId = $shippingData->getId();
                    $response[] = [
                        'status' => true,
                        'ship_id' => $shipId,
                        'shippingCountryCode' => $shippingCountryCode,
                        'shippingCountryName' => $shippingCountryName,
                        'shippingPrice' => $shippingPrice,
                        'shippingCurrency' => $shippingCurrency,
                        'Message' => 'Data saved in Magento'
                    ];
                }
                else{
                    $response[] = [
                        'shippingCountryCode' => $shippingCountryCode,
                        'Message' => 'Country already exist'
                    ];
                }

                //end
            }
            // Your Code here

       }

        catch(\Exception $e)
        {
        	$response[] = [
                'status' => 'Please note below exception',
                'message' => $e->getMessage()
            ];
            // $response = ['success' => false, 'message' => $e->getMessage() ];
            $this
                ->logger
                ->info($e->getMessage());

        }
        // $returnArray = json_encode($response);
        return $response;
    }

     /**
     * @inheritdoc
     */

    public function updatePost($shipId, $updatedShippingPrice)
    {
        $response = array();
        try
        {

            if(!empty($shipId)){
               //save data in shipping price table
                $shippingData = $this->gridFactory->create();
                $shipCollection = $shippingData->getCollection()->addFieldToFilter('entity_id',$shipId);
                $shipCount = count($shipCollection);
                if($shipCount > 0){
                    foreach($shipCollection as $shipData){
                        $shipData->setShippingPrice($updatedShippingPrice);
                        $shipData->save();
                        $response[] = [
                            'status' => true,
                            'ship_id' => $shipId,
                            'updated_shipping_price' => $updatedShippingPrice,
                            'Message' => 'Data updated in Magento'
                        ];
                    }
                }
                else{
                    $response[] = [
                            'status' => true,
                            'Message' => 'Id does not exist in Magento. Please check and try again'
                        ];
                }

                //end
            }

            // Your Code here


       }
        catch(\Exception $e)
        {
            $response[] = [
                'status' => 'Please note below exception',
                'message' => $e->getMessage()
            ];
            $this
                ->logger
                ->info($e->getMessage());

        }
        // $returnArray = json_encode($response);
        return $response;
    }

     /**
     * @inheritdoc
     */

    public function deletePost($shipId)
    {
        $response = array();
        try
        {

            if(!empty($shipId)){
               //save data in shipping price table
                $shippingData = $this->gridFactory->create();
                $shipCollection = $shippingData->getCollection()->addFieldToFilter('entity_id',$shipId);
                $shipCount = count($shipCollection);
                foreach($shipCollection as $shipData){
                    $shipData->delete();
                }
                //end
            }

            // Your Code here
            $response[] = [
                'status' => true,
                'ship_id' => $shipId,
                'Message' => 'Shipping data deleted'
            ];

       }
        catch(\Exception $e)
        {
            $response[] = [
                'status' => 'Please note below exception',
                'message' => $e->getMessage()
            ];
            $this
                ->logger
                ->info($e->getMessage());

        }
        // $returnArray = json_encode($response);
        return $response;
    }


    /**
    * Returns cronjobs data
    *
    * @api
    * @param  string $cronstatus Cron status.
    * @param  string $createdat Cron status.
    * @return string cron array collection.
    */
   public function getCronList($cronstatus, $createdat){
     $response = array();

     $createdStart = date('Y-m-d 00:00:00', strtotime($createdat));
     $createdEnd = date('Y-m-d 23:59:59', strtotime($createdat));

     $connection     = $this->resource->getConnection();
     //$sql            = "select * from cron_schedule WHERE status='".$cronstatus."' AND created_at >= '".$createdStart."' AND created_at <= '".$createdEnd."'";
     
     $sql = $connection->select()
     ->from('cron_schedule',['status','schedule_id','job_code','messages','created_at','scheduled_at','executed_at','finished_at','mpcronschedule_email_sent'])
     ->where('status = ?', $cronstatus)
     ->where('created_at >= ?', $createdStart)
     ->where('created_at <= ?', $createdEnd);

     $result = $connection->fetchAll($sql);

     if(count($result) > 0){
       try{
         foreach ($result as $cronData){
           $response[] = [
               'status' => true,
               'cronstatus' => $cronData['status'],
               'cron_id' => $cronData['schedule_id'],
               'job_code' => $cronData['job_code'],
               'cron_message' => $cronData['messages'],
               'created_at' => $cronData['created_at'],
               'scheduled_at' => $cronData['scheduled_at'],
               'executed_at' => $cronData['executed_at'],
               'finished_at' => $cronData['finished_at'],
               'mpcronschedule_email_sent' => $cronData['mpcronschedule_email_sent'],
               'Message' => 'Cron data returned successfully'
           ];
           //echo $jobCode = $cronData['job_code'];
         }
       }
       catch(\Exception $e)
       {
           $response[] = [
               'status' => 'Please note below exception',
               'message' => $e->getMessage()
           ];
       }
     }
     else{
       $response[] = [
           'status' => true,
           'Message' => 'No cron jobs with status '.$cronstatus.' on '.$createdat.'.'
       ];
     }

     return $response;
   }

}
