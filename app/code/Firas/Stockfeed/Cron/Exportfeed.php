<?php

namespace Firas\Stockfeed\Cron;

use Magento\Framework\App\Filesystem\DirectoryList;

class Exportfeed
{
    protected $logger;
	protected $stockItemRepository;

	public function __construct(
        \Psr\Log\LoggerInterface $loggerInterface,
		\Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Catalog\Model\ProductFactory $productModel,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockItemRepository
	) {
        $this->logger = $loggerInterface;
		$this->_fileFactory = $fileFactory;
        $this->directory    = $filesystem->getDirectoryWrite(DirectoryList::PUB);
        $this->resource     = $resource;
        $this->productModel = $productModel;
        $this->stockItemRepository = $stockItemRepository;
	}

    /**
     *
     * @return void
     */
    public function execute()
    {
        try
        {
			//test command line
			//php bin/magento cron:run --group="firas_stockfeed_cron_group"
			//$this->logger->debug('Firas\Stockfeed\Cron\Exportfeed');
            $filepath = 'export/product.csv';
            $this->directory->create('export');

            /* Open file */
            $stream = $this->directory->openFile($filepath, 'w');
            $stream->lock();

            $connection     = $this->resource->getConnection();
            $sql            = "Select entity_id FROM catalog_product_entity";
            $result         = $connection->fetchAll($sql);

            /* set header fields for csv*/
            $header = array('Product Id','Sku','Status','Quantity','Stock Status');
            $stream->writeCsv($header);

            foreach ($result as $product){
                /* write data fields for csv*/
                $product       = $this->productModel->create()->load($product['entity_id']);
                $stockInfo     = $this->getStockItemInformation($product['entity_id']);
                $stockQty      = $stockInfo['qty'];
                $stockStatus   = $stockInfo['is_in_stock'];

                $itemData      = [];
                $itemData[]    = $product->getId();
                $itemData[]    = $product->getSku();
                $itemData[]    = $product->getStatus();
                $itemData[]    = $stockQty;
                $itemData[]    = $stockStatus;
                $stream->writeCsv($itemData);
            }

            $csvfilename = 'product.csv';
            $content = [];
            $content['type'] = 'filename';
            $content['value'] = $filepath;
            $this->_fileFactory->create($csvfilename, $content, DirectoryList::PUB);
        }
        catch(\Exception $e)
        {
            $this->logger->debug($e->getMessage());
        }
    }

    public function getStockItemInformation($productId)
    {   
        return $this->stockItemRepository->getStockItem($productId)->getData();
    }
}