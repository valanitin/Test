<?php
/**
 * @Author      Firas Developers
 * @package     Firas_GiftCard
 * @copyright   Copyright (c) 2019 MAGETOP (https://www.firas.com)
 * @terms       https://www.firas.com/terms
 * @license     https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 **/
namespace Firas\GiftCard\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\App\RequestInterface;

class AfterProductSave implements ObserverInterface
{
    /** @var \Magento\Framework\Logger\Monolog */
    protected $_logger;

    /** @var Magento\Framework\App\RequestInterface */
    protected $_request;

    /**
     * @param \Psr\Log\LoggerInterface               $loggerInterface
     * @param RequestInterface                       $requestInterface
     */
    public function __construct(
        \Psr\Log\LoggerInterface $loggerInterface,
        RequestInterface $requestInterface
    ) {
        $this->_logger = $loggerInterface;
        $this->_request = $requestInterface;
    }

    /**
     * This is the method that fires when the event runs.
     *
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $product = $observer->getEvent()->getProduct();
        if ($product->getTypeId() == 'giftcard') {
            if (!$product->getHasOptions() && intval($product->getHasOptions())==0) {
                $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                $emailCustomOption = $objectManager->create('Magento\Catalog\Api\Data\ProductCustomOptionInterface');
                $messageCustomOption = $objectManager->create('Magento\Catalog\Api\Data\ProductCustomOptionInterface');
                $emailCustomOption->setTitle('Email To')
                             ->setType('field')
                             ->setIsRequire(true)
                             ->setSortOrder(0)
                             ->setPrice(0)
                             ->setPriceType('percent')
                             ->setIsDelete(0)
                             ->setProductSku($product->getSku());
                $customOptions[] = $emailCustomOption;
                $messageCustomOption->setTitle('Message')
                             ->setType('area')
                             ->setIsRequire(true)
                             ->setSortOrder(0)
                             ->setPrice(0)
                             ->setPriceType('percent')
                             ->setMaxCharacters(160)
                             ->setIsDelete(0)
                             ->setProductSku($product->getSku());
                $customOptions[] = $messageCustomOption;
                $product->setOptions($customOptions)->save();
            }
        }
    }
}
