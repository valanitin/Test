<?php
/**
 * @package     Plumrocket_Base
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license/  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\Base\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\View\Page\Config as PageConfig;
use Plumrocket\Base\Model\Theme\GetInformation;

/**
 * @since 2.3.0
 */
class AddClassToBodyObserver implements ObserverInterface
{
    const CSS_CLASS_PREFIX = 'pl-thm-';

    /** @var PageConfig */
    private $pageConfig;

    /**
     * @var \Plumrocket\Base\Model\Theme\GetInformation
     */
    private $getThemeInformation;

    /**
     * @param \Magento\Framework\View\Page\Config         $pageConfig
     * @param \Plumrocket\Base\Model\Theme\GetInformation $getThemeInformation
     */
    public function __construct(
        PageConfig $pageConfig,
        GetInformation $getThemeInformation
    ) {
        $this->pageConfig = $pageConfig;
        $this->getThemeInformation = $getThemeInformation;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(Observer $observer)
    {
        $this->pageConfig
            ->addBodyClass(self::CSS_CLASS_PREFIX . $this->getThemeInformation->getVendor())
            ->addBodyClass(self::CSS_CLASS_PREFIX . $this->getThemeInformation->getName());
    }
}
