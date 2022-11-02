<?php 

namespace Dynamic\Cmspagemanager\Model;

class CmsConfig extends \Magento\Framework\Model\AbstractModel {
	
	protected $_cmsPage;

	protected $_search;

	public function __construct(
	    \Magento\Cms\Api\PageRepositoryInterface $pageRepository,
	    \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
	) {
	    $this->_cmsPage = $pageRepository;
	    $this->_search = $searchCriteriaBuilder;
	}

	public function getCmsArray() {

		$pages = [];
	    foreach($this->_cmsPage->getList($this->_getSearchCriteria())->getItems() as $page) {
	        $pages[$page->getPageId()] = $page->getTitle();
	    }
	    return $pages;
	}

	protected function _getSearchCriteria()
	{
	    return $this->_search->addFilter('is_active', '1')->create();
	}
	
}
