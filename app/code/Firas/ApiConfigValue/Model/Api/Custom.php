<?php
namespace Firas\ApiConfigValue\Model\Api;

use Psr\Log\LoggerInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;


class Custom
{
    protected $logger;
    
    /**
     * @var \Magento\Framework\App\Config\Storage\WriterInterface
    */
    protected $configWriter;

    protected $cacheTypeList;

    /**
     * @var \Magento\Framework\Webapi\Rest\Request
     */
    protected $request;

    protected $_scopeConfig;

    public function __construct(
        LoggerInterface $logger, 
        WriterInterface $configWriter,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Framework\Webapi\Rest\Request $request
    ) {
        $this->logger = $logger;
        $this->configWriter = $configWriter;
        $this->_scopeConfig = $scopeConfig;
        $this->cacheTypeList = $cacheTypeList;
        $this->request = $request;
    }

     /**
     * @inheritdoc
     */

    public function updateConfigValue()
    {   
        $path = $this->request->getParam('path');
        $value = $this->request->getParam('value');
        $scopeId = $this->request->getParam('scopeId');
        $scope = $this->request->getParam('scope');
        $returnArray = array();

        try {
            $this->configWriter->save($path, $value, $scope, $scopeId);
            $returnArray['status'] = true;
            return $returnArray;
        }
        catch (\Exception $e) {
            $this->logger->info($e->getMessage());
            $returnArray['status'] = false;
            return $returnArray;
        }

        $this->clearCache();
    }

    public function getConfigValue($data)
    {   

        $returnArray = array();
		foreach($data as $key => $value)
		{
			try {
				$value = $this->_scopeConfig->getValue($value['path'],$value['scope'], $value['scope_id']);
				if($value==''){
					$returnArray[$key]['status'] = 'error';
					$returnArray[$key]['value'] = "Please check parameter";
				}else{
					$returnArray[$key]['status'] = 'success';
					$returnArray[$key]['value'] = $value;
				}
			}
			catch (\Exception $e) {
				$this->logger->info($e->getMessage());
				$returnArray[$key]['status'] = 'error';
				$returnArray[$key]['value'] = $e->getMessage();
			}
		}
        return $returnArray;
    }

    public function clearCache()
    {
        $this->cacheTypeList
            ->cleanType(\Magento\Framework\App\Cache\Type\Config::TYPE_IDENTIFIER);
        $this->cacheTypeList
            ->cleanType(\Magento\PageCache\Model\Cache\Type::TYPE_IDENTIFIER);
    }
}
