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

namespace Plumrocket\Amp\Block\Catalog\Product;

/**
 * Class ListForm
 *
 * @package Plumrocket\Amp\Block\Catalog\Product
 *
 * @method getStatePrefix()
 * @method setStatePrefix($statePrefix)
 */
class ListForm extends \Magento\Framework\View\Element\Template
{
    const MESSAGE_BLOCK_NAME = 'amp.global.ajax.product.list.form.message';

    /**
     * @var \Plumrocket\Amp\Helper\Form
     */
    private $ampFormHelper;

    /**
     * @var ListForm\ButtonRendererInterface[]
     */
    private $buttonRenderer;

    /**
     * @var \Plumrocket\Amp\ViewModel\GlobalState
     */
    private $globalState;

    /**
     * ListForm constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Plumrocket\Amp\Helper\Form                      $ampFormHelper
     * @param \Plumrocket\Amp\ViewModel\GlobalState            $globalState
     * @param array                                            $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Plumrocket\Amp\Helper\Form $ampFormHelper,
        \Plumrocket\Amp\ViewModel\GlobalState $globalState,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->ampFormHelper = $ampFormHelper;
        $this->globalState = $globalState;
    }

    /**
     * @return string
     */
    public function getStateName()
    {
        return $this->globalState->getStateName();
    }

    /**
     * @return string
     */
    public function getFormId()
    {
        return $this->getStateName() . 'Id';
    }

    /**
     * @return string
     */
    public function getProductIdStatePath()
    {
        return $this->getStateName() . '.' . implode('.', $this->getAmpMessagesBlock()->getProductIdStatePath());
    }

    /**
     * @return string
     */
    public function getActionUrl()
    {
        return $this->ampFormHelper->getAmpAddToCartUrl();
    }

    /**
     * @return string
     */
    public function getFormMessageEvents()
    {
        return $this->getAmpMessagesBlock()->getFormMessageEvents();
    }

    /**
     * @return bool|\Plumrocket\Amp\Block\Page\Form\Product\Message
     */
    public function getAmpMessagesBlock() // @codingStandardsIgnoreLine
    {
        return $this->getLayout()->getBlock(self::MESSAGE_BLOCK_NAME);
    }

    /**
     * @param ListForm\ButtonRendererInterface $renderer
     * @return $this
     */
    public function addButtonRenderer(ListForm\ButtonRendererInterface $renderer)
    {
        $renderer->setStateName($this->globalState->getStateName())
                 ->setProductIdPath($this->getAmpMessagesBlock()->getProductIdStatePath())
                 ->setFormId($this->getFormId());

        $this->buttonRenderer[] = $renderer;

        return $this;
    }

    /**
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     * @return string
     */
    public function getProductButtonsHtml($product)
    {
        $html = '';
        foreach ($this->buttonRenderer as $buttonRenderer) {
            $html .= $buttonRenderer->renderByProduct($product);
        }

        return $html;
    }
}
