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
 * @copyright   Copyright (c) 2018 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Amp\Block\Adminhtml\Widget;

use Magento\Framework\Data\Form\Element\Label as Element;

/**
 * Class ImageChooser
 *
 * @method array getConfig()
 */
class ImageChooser extends \Magento\Backend\Block\Template
{
    /**
     * @var \Magento\Framework\Data\Form\Element\Factory
     */
    private $elementFactory;

    /**
     * ImageChooser constructor.
     *
     * @param \Magento\Backend\Block\Template\Context      $context
     * @param \Magento\Framework\Data\Form\Element\Factory $elementFactory
     * @param array                                        $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Data\Form\Element\Factory $elementFactory,
        $data = []
    ) {
        $this->elementFactory = $elementFactory;
        parent::__construct($context, $data);
    }

    /**
     * Prepare chooser element HTML
     *
     * @param Element $element
     * @return Element
     */
    public function prepareElementHtml(Element $element)
    {
        $config = $this->_getData('config');
        $sourceUrl = $this->getUrl(
            'cms/wysiwyg_images/index',
            ['target_element_id' => $element->getId(), 'type' => 'file']
        );

        /** @var \Magento\Backend\Block\Widget\Button $chooser */
        $chooser = $this->getLayout()->createBlock(\Magento\Backend\Block\Widget\Button::class)
            ->setType('button')
            ->setClass('btn-chooser')
            ->setLabel($config['button']['open'])
            ->setOnClick('MediabrowserUtility.openDialog(\''. $sourceUrl .'\', null, null, '
                . '\'' .$this->escapeJs(__($config['button']['open'])) . '\','
                . '{closed: function(e, modal) {'
                    . 'PRAMP.parseLink(modal)'
                . ' }})')
            ->setDisabled($element->getReadonly());

        $input = $this->elementFactory->create('hidden', ['data' => $element->getData()]);
        $input->setId($element->getId());
        $input->setForm($element->getForm());
        $input->setClass('widget-option input-text admin__control-text');
        if ($element->getRequired()) {
            $input->addClass('required-entry');
        }

        $imageUrl = '';
        $mediaUrl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        if ($element->getValue()) {
            $imageUrl = $mediaUrl . $element->getValue();
        }

        $imageHtml = '<img class="amp-banner" data-media-url="' . $mediaUrl . '" src="' . $imageUrl . '" />';

        $js = "<script>require(['pramp'], function (PRAMP) {'use strict'; PRAMP.insertSize();});</script>";

        $element->setData('after_element_html', $imageHtml . $input->getElementHtml() . $chooser->toHtml() . $js);

        return $element;
    }
}
