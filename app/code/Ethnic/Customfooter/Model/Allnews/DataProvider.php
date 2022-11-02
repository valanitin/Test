<?php
namespace Ethnic\Customfooter\Model\Allnews;

use Ethnic\Customfooter\Model\ResourceModel\Allnews\CollectionFactory;
use Magento\Framework\App\Request\DataPersistorInterface;
 
class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
	protected $collection;

	protected $dataPersistor; 

	protected $loadedData;

	public function __construct(
		$name,
		$primaryFieldName,
		$requestFieldName,
		CollectionFactory $allnewsCollectionFactory,
		DataPersistorInterface $dataPersistor,
		array $meta = [],
		array $data = []
	){
		$this->collection = $allnewsCollectionFactory->create();
		$this->dataPersistor = $dataPersistor;
		parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
		$this->meta = $this->prepareMeta($this->meta);
	}
	public function prepareMeta(array $meta)
    {
    	return $meta; 
    }	
    public function getData()
    {
    	if (isset ($this->loadedData)){
    		return $this->loadedData;
    	}
    	$items = $this->collection->getItems();
    	foreach ($items as $news){
    		$this->loadedData[$news->getId()] = $news->getData();
    	}

    	$data = $this->dataPersistor->get('news_allnews');
    	if(!empty($data)) {
    		$news = $this->collection->getNewEmptyItem();
    		$news->setData($data);
    		$this->loadedData[$news->getId()] = $news->getData();
    		$this->dataPersistor->clear('news_allnews');
    	}
    	return $this->loadedData;
    }
}
?>