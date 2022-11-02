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
 * @package     Plumrocket Amp v2.x.x
 * @copyright   Copyright (c) 2017 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Amp\Model\Plugin\Magento\Swatches\LayeredNavigation;

use Plumrocket\Amp\Helper\Data as DataHelper;
use Magento\Swatches\Block\LayeredNavigation\RenderLayered as RenderLayered;

/**
 * Block RenderLayered Magento_Swatches
 */
class RenderLayeredPlugin
{
    /**
     * @var \Plumrocket\Amp\Helper\Data
     */
    protected $_dataHelper;

    /**
     * Path to AMP-template file.
     *
     * @var string
     */
    protected $_template = 'Plumrocket_Amp::catalog/product/layered/renderer.phtml';

    /**
     * @param DataHelper $dataHelper
     */
    public function __construct(
        DataHelper $dataHelper
    ) {
        $this->_dataHelper = $dataHelper;
    }

    /**
     * @param  RenderLayered
     * @param  string $result
     * @return string $result
     */
    public function afterGetTemplate(RenderLayered $subject, $result)
    {
        if (!$this->_dataHelper->isAmpRequest()){
            return $result;
        }

        return $this->_template;
    }
}
