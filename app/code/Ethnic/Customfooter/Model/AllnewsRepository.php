<?php
namespace Ethnic\Customfooter\Model;

use Ethnic\Customfooter\Api\Data;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Ethnic\Customfooter\Model\ResourceModel\Allnews as ResourceAllnews;
use Ethnic\Customfooter\Model\ResourceModel\Allnews\CollectionFactory as AllnewsCollectionFactory;
use Magento\Store\Model\StoreManagerInterface;

class AllnewsRepository
{
    protected $resource;

    protected $allnewsFactory;

    protected $dataObjectHelper;

    protected $dataObjectProcessor;

    protected $dataAllnewsFactory;

    private $storeManager;

    public function __construct(
        ResourceAllnews $resource,
        AllnewsFactory $allnewsFactory,
        Data\AllnewsInterfaceFactory $dataAllnewsFactory,
        DataObjectHelper $dataObjectHelper,
		DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager
    ) {
        $this->resource = $resource;
		$this->allnewsFactory = $allnewsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataAllnewsFactory = $dataAllnewsFactory;
		$this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
    }

    public function save(\Ethnic\Customfooter\Api\Data\AllnewsInterface $news)
    {
        if ($news->getStoreId() === null) {
            $storeId = $this->storeManager->getStore()->getId();
            $news->setStoreId($storeId);
        }
        try {
            $this->resource->save($news);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(
                __('Could not save the news: %1', $exception->getMessage()),
                $exception
            );
        }
        return $news;
    }

    public function getById($enabledId)
    {
		$news = $this->allnewsFactory->create();
        $news->load($enabledId);
        if (!$news->getId()) {
            throw new NoSuchEntityException(__('News with id "%1" does not exist.', $enabledId));
        }
        return $news;
    }
	
    public function delete(\Ethnic\Customfooter\Api\Data\AllnewsInterface $news)
    {
        try {
            $this->resource->delete($news);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the news: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    public function deleteById($enabledId)
    {
        return $this->delete($this->getById($enabledId));
    }

    /**
    * GET for Post api
    * @return array
    */
    public function getFooterData($id, $formKey){
        return $id;
    }

}
?>