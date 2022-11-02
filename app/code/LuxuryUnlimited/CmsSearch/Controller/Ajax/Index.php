<?php

namespace LuxuryUnlimited\CmsSearch\Controller\Ajax;

use \MageWorx\SearchSuiteAutocomplete\Helper\Data as HelperData;
use \Magento\Framework\App\Action\Context;
use \Magento\Framework\Controller\ResultFactory;
use \Magento\Search\Model\QueryFactory;
use \Magento\Store\Model\StoreManagerInterface;
use \MageWorx\SearchSuiteAutocomplete\Model\Search as SearchModel;
use \Magento\Cms\Model\PageFactory;
use \Magento\Cms\Helper\Page as Helper;

/**
 * SearchSuiteAutocomplete ajax controller
 */
class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \MageWorx\SearchSuiteAutocomplete\Helper\Data
     */
    protected $helperData;

    /**
     * @var \Magento\Search\Model\QueryFactory
     */
    private $queryFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \MageWorx\SearchSuiteAutocomplete\Model\Search
     */
    private $searchModel;

    /**
     * @var \Magento\Cms\Model\PageFactory
     */
    private $pageFactory;

    /**
     * @var \Magento\Cms\Helper\Page
     */
    private $helper;

    /**
     * Index constructor.
     *
     * @param HelperData $helperData
     * @param Context $context
     * @param QueryFactory $queryFactory
     * @param StoreManagerInterface $storeManager
     * @param SearchModel $searchModel
     * @param PageFactory $pageFactory
     * @param Helper $helper
     */
    public function __construct(
        HelperData $helperData,
        Context $context,
        QueryFactory $queryFactory,
        StoreManagerInterface $storeManager,
        SearchModel $searchModel,
        PageFactory $pageFactory,
        Helper $helper
    ) {
        $this->helperData   = $helperData;
        $this->storeManager = $storeManager;
        $this->queryFactory = $queryFactory;
        $this->searchModel  = $searchModel;
        $this->pageFactory = $pageFactory;
        $this->helper = $helper;
        parent::__construct($context);
    }

    /**
     * Retrieve json of result data
     *
     * @return array|\Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        $query = $this->queryFactory->get();
        $query->setStoreId($this->storeManager->getStore()->getId());

        $responseData = [];

        if ($query->getQueryText() != '') {

            $query->setId(0)->setIsActive(1)->setIsProcessed(1);
            
            $queryText = $this->queryFactory->get()->getQueryText();

            $pagesCollection = $this->pageFactory->create()->getCollection()
                                            ->addFieldToFilter('is_active',1)
                                            ->addFieldToFilter('title',['like' => '%'.$queryText.'%'])
                                            ->addFieldToFilter('content',['like' => '%'.$queryText.'%']);
            $pageData = [];
            
            $pageData[2]['code'] = "cms";
            foreach($pagesCollection as $page)
            {
                $pageData[2]['data'][] = [
                    'name' => $page->getTitle(),
                    'url' => $this->helper->getPageUrl($page->getPageId())
                ];
            }

            $result = array_merge($this->searchModel->getData(),$pageData);

            $responseData['result'] = $result;
            
        }

        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setData($responseData);

        return $resultJson;
    }
}
