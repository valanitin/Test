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
 * @copyright   Copyright (c) 2019 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Amp\Ui\Component\Form\Cms\Page\Edit;

class AmpContentFieldset extends \Magento\Ui\Component\Form\Fieldset
{
    /**
     * @var \Plumrocket\Amp\Helper\Data
     */
    private $dataHelper;

    /**
     * AmpContentFieldset constructor.
     *
     * @param \Magento\Framework\View\Element\UiComponent\ContextInterface $context
     * @param \Plumrocket\Amp\Helper\Data                                  $dataHelper
     * @param array                                                        $components
     * @param array                                                        $data
     */
    public function __construct(
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Plumrocket\Amp\Helper\Data $dataHelper,
        $components = [],
        array $data = []
    ) {
        parent::__construct($context, $components, $data);
        $this->dataHelper = $dataHelper;
    }

    /**
     * Get component configuration
     *
     * @return array
     */
    public function getConfiguration()
    {
        $config = parent::getConfiguration();

        $config['visible'] = $this->dataHelper->moduleEnabled();

        return $config;
    }
}
