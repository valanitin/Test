<?php
namespace Ethnic\AdroidApp\Controller\Api;

use Magento\Catalog\Model\Layer\Resolver;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Controller\Result\JsonFactory;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
*/
class FilterProductList extends \Magento\Framework\App\Action\Action
{
	
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    protected $productcollection;

    private $resultJsonFactory;

    private $productFactory;

    protected $ethnicHelper;
    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productcollection,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        JsonFactory $resultJsonFactory
    ) {
        parent::__construct($context);
        $this->_storeManager = $storeManager;
        $this->productcollection = $productcollection;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->productFactory = $productFactory; 
    }


    /**
     * Category view action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
    	$resultJson = $this->resultJsonFactory->create();

        $filterParamentersArray = $this->getFilterParameters();


        $filterParamenters = $filterParamentersArray['Filterparametires'];
       	
		$category = $filterParamentersArray['catId'];

		$customerId = $this->getRequest()->getParam('customerId');

		$page=($this->getRequest()->getParam('page'))? $this->getRequest()->getParam('page') : 1;

		$priceorder = ($this->getRequest()->getParam('order'))? $this->getRequest()->getParam('order') : 1;
		//get values of current limit. If not the param value then it will set to 1
		$pageSize=($this->getRequest()->getParam('limit'))? $this->getRequest()->getParam('limit') : 1;


            $productcollectionarray = $this->productcollection->create();
	   		$productcollectionarray->addAttributeToSelect("*");
        if ($category) {
	        $productcollectionarray->addCategoriesFilter(['eq'=>$category]);
		}
	       	$productcollectionarray->addAttributeToFilter('status', ['eq' => 1]);
	       	// $productcollectionarray->addAttributeToFilter('image', ['neq' => null]);
	       	$productcollectionarray->addAttributeToFilter('visibility',4);
			$productcollectionarray->setPageSize($pageSize);
			$productcollectionarray->setCurPage($page);
           	foreach($filterParamenters as $filter)
			{

				$filterattribute = $filter['field'];
				$filtervalue = $filter['value'];
				$filteropration = $filter['condition_type'];
				if($filteropration == 'in')
				{
					$filtervalue = explode(',',$filtervalue);
				}
				if($filteropration == 'finset')
				{
					$filterarray = array();
					$filtervalue = explode(',',$filtervalue);
					foreach($filtervalue as $fvalue)
					{
						$filterarray[] = array(
												'attribute' => $filterattribute,
					        					 $filteropration => $fvalue
					        				  ); 
					}	

					if (count($filterarray) > 0) {
					    $productcollectionarray->addAttributeToFilter($filterarray);
					}
					
				}
				
				if($filteropration != 'finset' && $filtervalue != '' && $filteropration != 'range')
				{
					$productcollectionarray->addAttributeToFilter(
										            array(
										                array('attribute'=>$filterattribute,$filteropration=>$filtervalue)
										            )
										    );					
				}
				if($filteropration == 'range' && $filtervalue != '')
				{
					$pricevalue = explode("_", $filtervalue);

		          	$productcollectionarray->addFieldToFilter('price', array(
		                                array('gt' => $pricevalue[0], 'lt' => $pricevalue[1]),
		                            )
		                        );	

		          	$productcollectionarray->addFieldToFilter('price', array(
		                                array('lt' => $pricevalue[1]),
		                            )
		                        );

		               	
				}

			}

			$productcollectionarray->getSelect()->order('created_at', \Magento\Framework\DB\Select::SQL_DESC);		

			$productcollectionarray = $productcollectionarray->getData();			       
			

			$response = [
				'error' => false,
				'message' => 'Success',
				'data' => $productcollectionarray
			];

        

        $resultJson->setHeader('Access-Control-Allow-Origin', "*", true);
        
		return $resultJson->setData($response);

    }

    public function getFilterParameters(){
    	$post = $this->getRequest()->getParams();

    	$filterarray = [];

    	$catid = [];

    	if(isset($post['filterarray']))
    	{
    		$filterarrayjson = json_decode($post['filterarray'],true);
    		
    		if(isset($filterarrayjson['Filterparametires']))
    		{
    			$filterarray = $filterarrayjson['Filterparametires'];
    		}
    		if(isset($filterarrayjson['catId']['id']))
    		{
    			$catid = $filterarrayjson['catId']['id'];
    		}


    	}
   

    	$filterfinalarray = [];	


		$filterfinalarray = [
			'Filterparametires' => $filterarray,
			'catId' => $catid
		];



    	return $filterfinalarray;

    }

}