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
 * @package     Plumrocket_Amp 2.x.x
 * @copyright   Copyright (c) 2016 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Amp\Block\Catalog\LayeredNavigation;

use Magento\Eav\Model\Entity\Attribute;
use Magento\Catalog\Model\ResourceModel\Layer\Filter\AttributeFactory;

class RenderLayered extends \Magento\Swatches\Block\LayeredNavigation\RenderLayered
{
    /**
     * @var \Plumrocket\Amp\Helper\Data
     */
    private $dataHelper;

    /**
     * RenderLayered constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param Attribute                                        $eavAttribute
     * @param AttributeFactory                                 $layerAttribute
     * @param \Magento\Swatches\Helper\Data                    $swatchHelper
     * @param \Magento\Swatches\Helper\Media                   $mediaHelper
     * @param \Plumrocket\Amp\Helper\Data                      $dataHelper
     * @param array                                            $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        Attribute $eavAttribute,
        AttributeFactory $layerAttribute,
        \Magento\Swatches\Helper\Data $swatchHelper,
        \Magento\Swatches\Helper\Media $mediaHelper,
        \Plumrocket\Amp\Helper\Data $dataHelper,
        array $data = []
    ) {
        $this->dataHelper = $dataHelper;
        $this->eavAttribute = $eavAttribute;
        $this->layerAttribute = $layerAttribute;
        $this->swatchHelper = $swatchHelper;
        $this->mediaHelper = $mediaHelper;

        if ($this->dataHelper->isAmpRequest()) {
            $this->_template = 'Plumrocket_Amp::catalog/product/layered/renderer.phtml';
        }

        parent::__construct($context, $eavAttribute, $layerAttribute, $swatchHelper, $mediaHelper, $data);
    }
}
