<?php
 
namespace Ethnic\Footerapi\Model\Api;

use Ethnic\Customfooter\Model\AllnewsFactory;
use Ethnic\Customfooter\Model\ResourceModel\Allnews\CollectionFactory;
use Psr\Log\LoggerInterface;
 
class Footer
{
    protected $logger;
    protected $allnewslocal;
 
    public function __construct(
        LoggerInterface $logger,
        AllnewsFactory $allNews,
        CollectionFactory $collectionFactory)
    {
        $this->allnewslocal = $allNews;
        $this->logger = $logger;
        $this->collectionFactory = $collectionFactory;
    }
 
    /**
     * @inheritdoc
     */
 
    public function getFooterData()
    {
        $newsInstace = $this->collectionFactory->create()->addFieldToFilter("is_parent",1);
        $result = [];
        foreach ($newsInstace as $key => $value) {
            $childarr = [];
            $child = $this->collectionFactory->create()->addFieldToFilter('parent_id',$value['id']);
            foreach ($child as $k => $v) {
                $childarr[] = [
                                        "title" => $v['title'] , 
                                        "id" => $v['cms_page_id'],
                                    ];
            }   
            $result[] = ["title" => $value['title'], "submanu" => $childarr];
        }
        return $result;
    }
     
}