<?php
namespace Firas\Bodyclass\Plugin;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\View\Page\Config;
use Magento\Store\Model\StoreManagerInterface;
class BodyStoreClass implements ObserverInterface
{
    protected $config;
    protected $storeManager;
    public function __construct(
        Config $config,
        StoreManagerInterface $storeManager
    ){
        $this->config = $config;
        $this->storeManager = $storeManager;
    }
    public function execute(Observer $observer){
        $store = $this->storeManager->getStore();
        $storeCode = $store->getCode();
        $websiteCode = $store->getWebsite()->getCode();
        $this->config->addBodyClass($storeCode);
        $this->config->addBodyClass($websiteCode);
    }
}