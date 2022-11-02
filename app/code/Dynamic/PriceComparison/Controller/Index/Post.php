<?php

namespace Dynamic\PriceComparison\Controller\Index;

class Post extends \Magento\Framework\App\Action\Action
{   
    /**
    * Store manager
    *
    * @var \Magento\Framework\Controller\Result\JsonFactory
    */
    protected $resultJsonFactory;

    /**
    * Store manager
    *
    * @var \Magento\Store\Model\StoreManagerInterface
    */
    protected $storeManager;

    /**
    * CurlFactory
    *
    * @var \Magento\Framework\HTTP\Client\CurlFactory
    */
    protected $curlFactory;

    /**
    * Scope config
    *
    * @var \Magento\Framework\App\Config\ScopeConfigInterface
    */
    protected $scopeConfig;

    /**
    * Price Helper
    *
    * @var \Magento\Framework\Pricing\Helper\Data
    */
    protected $priceHelper;

    /**
    * Constructor
    *
    * @param \Magento\Framework\App\Action\Context  $context
    * @param \Magento\Store\Model\StoreManagerInterface $storeManager
    * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    * @param \Magento\Framework\HTTP\Client\CurlFactory $curlFactory
    * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    * @param \Magento\Framework\Pricing\Helper\Data $priceHelper
    */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\HTTP\Client\CurlFactory $curlFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Pricing\Helper\Data $priceHelper
    ) { 
        $this->storeManager = $storeManager;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->curlFactory = $curlFactory;
        $this->scopeConfig = $scopeConfig;
        $this->priceHelper = $priceHelper;
        parent::__construct($context);
    }

    /**
    * Execute view action
    *
    * @return \Magento\Framework\Controller\Result\JsonFactory
    */
    public function execute()
    {
        $productSku = $this->getRequest()->getParam("product_sku");

        $languageCode = $this->storeManager->getStore()->getCode();
        $countryId = $this->storeManager->getStore()->getCurrentCurrencyCode();

        $response = [];

        if($productSku){

            $newjsonData = [
                "sku" => $productSku,
                "country" => $countryId,
                "lang_code" => $languageCode
            ];

            $url = 'https://erp.theluxuryunlimited.com/api/price_comparision/details';
            $curl = $this->curlFactory->create();
            $curl->setOption(CURLOPT_RETURNTRANSFER, 1);
            $curl->setOption(CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
            $curl->post($url, json_encode($newjsonData));

            $result   = $curl->getBody();
            $decodeResponse = json_decode($result,true);

            $html = '';

            if(!empty($decodeResponse) && isset($decodeResponse['status']) && $decodeResponse['status'] == "success") {
                if(!empty($decodeResponse['results']) && isset($decodeResponse['results'])) {

                    $html .= "<div class='price-comparison-item-data'>";

                    foreach ($decodeResponse['results'] as $results) {
                        $html .= "<div class='price-comparison-item'>";
                        $html .= "<div class='price-comparison-name'>";
                        $html .= "<p>".$results['name']."</p>";
                        $html .= "</div>";
                        $html .= "<div class='price-comparison-price'>";
                        $html .= "<p>".$this->priceHelper->currency($results['price'], true, false)."</p>";
                        $html .= "</div>";
                        $html .= "</div>";
                    }
                    $html .= "</div>";

                    $response = [
                        "errors" => false,
                        "html" => $html,
                        "casetype" => 1,
                        "casemessage" => __("If you feel that we need to add more price comparisons or these are incorrect please raise a ticket and we will look into it and share further information with you at the earliest")
                    ];
                } else {
                    $response = [
                        "errors" => true,
                        "message" => "Compare price not found.",
                        "casetype" => 2,

                        "casemessage" => "It seems that we do not have any price comparison data for this product , please raise a ticket here so we can review it further and revert to you with more details."
                    ];
                }
            } else {
               $response = [
                        "errors" => true,
                        "message" => "Compare price not found.",
                        "casetype" => 2,

                        "casemessage" => "It seems that we do not have any price comparison data for this product , please raise a ticket here so we can review it further and revert to you with more details."
                    ];
            }
        }

        $resultJson = $this->resultJsonFactory->create();
        return $resultJson->setData($response);
    }
}

