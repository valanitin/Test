<?php
/**
 * @package     Plumrocket_Base
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license/  End-user License Agreement
 */

namespace Plumrocket\Base\Observer\Adminhtml;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Event\ObserverInterface;
use Plumrocket\Base\Model\IsModuleInMarketplace;

/**
 * @since 2.1.6
 */
class LayoutLoadBeforeObserver implements ObserverInterface
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;

    /**
     * @var \Plumrocket\Base\Model\IsModuleInMarketplace
     */
    private $isModuleInMarketplace;

    /**
     * LayoutLoadBeforeObserver constructor.
     *
     * @param \Magento\Framework\App\RequestInterface      $request
     * @param \Plumrocket\Base\Model\IsModuleInMarketplace $isModuleInMarketplace
     */
    public function __construct(
        RequestInterface $request,
        IsModuleInMarketplace $isModuleInMarketplace
    ) {
        $this->request = $request;
        $this->isModuleInMarketplace = $isModuleInMarketplace;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ('adminhtml_system_config_edit' === $this->request->getFullActionName()
            && $this->isModuleInMarketplace->execute('Plumrocket_Base')
        ) {
            /** @var \Magento\Framework\View\Layout\ProcessorInterface $update */
            $update = $observer->getEvent()->getLayout()->getUpdate();
            $update->addUpdate('<head><css src="Plumrocket_Base::css/system/config.css"/></head>');
        }
    }
}
