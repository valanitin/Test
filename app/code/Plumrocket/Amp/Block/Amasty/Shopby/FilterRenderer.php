<?php
/**
 * Plumrocket Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End-user License Agreement
 * that is available through the world-wide-web at this URL:
 * http://wiki.plumrocket.net/wiki/EULA
 * If you are unable to obtain it through the world-wide-web, please
 * send an email to support@plumrocket.com so we can send you a copy immediately.
 *
 * @package     Plumrocket_Amp
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\Amp\Block\Amasty\Shopby;

use Magento\Catalog\Model\Layer\Filter\FilterInterface;
use Magento\Framework\Module\Manager;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\View\Element\Template\Context;
use Plumrocket\Amp\Helper\Data;

/**
 * @since 2.9.16
 */
class FilterRenderer extends \Magento\LayeredNavigation\Block\Navigation\FilterRenderer
{
    /**
     * @var \Plumrocket\Amp\Helper\Data
     */
    private $dataHelper;

    /**
     * @var \Magento\Framework\Module\Manager
     */
    private $moduleManager;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Plumrocket\Amp\Helper\Data                      $dataHelper
     * @param \Magento\Framework\Module\Manager                $moduleManager
     * @param \Magento\Framework\ObjectManagerInterface        $objectManager
     * @param array                                            $data
     */
    public function __construct(
        Context $context,
        Data $dataHelper,
        Manager $moduleManager,
        ObjectManagerInterface $objectManager,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->dataHelper = $dataHelper;
        $this->moduleManager = $moduleManager;
        $this->objectManager = $objectManager;
    }

    /**
     * Fix for Amasty_Shopby v.2.11.2
     * Use magento FilterRenderer::render in AMP mode
     *
     * @param \Magento\Catalog\Model\Layer\Filter\FilterInterface $filter
     * @return string
     */
    public function render(FilterInterface $filter): string
    {
        if ($this->dataHelper->isAmpRequest()) {
            return parent::render($filter);
        }

        if ($this->moduleManager->isEnabled('Amasty_Shopby')
            && class_exists('Amasty\Shopby\Block\Navigation\FilterRenderer')
        ) {
            $amastyFilterRenderer = $this->objectManager->create('Amasty\Shopby\Block\Navigation\FilterRenderer');
            return $amastyFilterRenderer->render($filter);
        }

        return parent::render($filter);
    }
}
