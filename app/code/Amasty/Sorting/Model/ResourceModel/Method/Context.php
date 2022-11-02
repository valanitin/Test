<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Sorting
 */


namespace Amasty\Sorting\Model\ResourceModel\Method;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Model\ResourceModel\Db\ObjectRelationProcessor;
use Magento\Framework\Model\ResourceModel\Db\TransactionManagerInterface;
use Magento\Framework\ObjectManager\ContextInterface;
use Magento\Store\Model\StoreManagerInterface;

class Context extends \Magento\Framework\Model\ResourceModel\Db\Context implements ContextInterface
{
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * Store manager
     *
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var \Amasty\Sorting\Helper\Data
     */
    private $helper;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    private $date;

    /**
     * Context constructor.
     *
     * @param \Magento\Framework\App\ResourceConnection   $resource
     * @param TransactionManagerInterface                 $transactionManager
     * @param ObjectRelationProcessor                     $objectRelationProcessor
     * @param ScopeConfigInterface                        $scopeConfig
     * @param RequestInterface                            $request
     * @param StoreManagerInterface                       $storeManager
     * @param \Amasty\Sorting\Helper\Data                 $helper
     * @param \Psr\Log\LoggerInterface                    $logger
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     */
    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        TransactionManagerInterface $transactionManager,
        ObjectRelationProcessor $objectRelationProcessor,
        ScopeConfigInterface $scopeConfig,
        RequestInterface $request,
        StoreManagerInterface $storeManager,
        \Amasty\Sorting\Helper\Data $helper,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Stdlib\DateTime\DateTime $date
    ) {
        parent::__construct($resource, $transactionManager, $objectRelationProcessor);
        $this->scopeConfig  = $scopeConfig;
        $this->request      = $request;
        $this->storeManager = $storeManager;
        $this->helper       = $helper;
        $this->logger       = $logger;
        $this->date         = $date;
    }

    /**
     * @return ScopeConfigInterface
     */
    public function getScopeConfig()
    {
        return $this->scopeConfig;
    }

    /**
     * @return StoreManagerInterface
     */
    public function getStoreManager()
    {
        return $this->storeManager;
    }

    /**
     * @return RequestInterface
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return \Amasty\Sorting\Helper\Data
     */
    public function getHelper()
    {
        return $this->helper;
    }

    /**
     * @return \Psr\Log\LoggerInterface
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * @return \Magento\Framework\Stdlib\DateTime\DateTime
     */
    public function getDate()
    {
        return $this->date;
    }
}
