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

namespace Plumrocket\Amp\Block\Catalog\Product\ListForm;

use Magento\Framework\View\Element\Block\ArgumentInterface;

/**
 * Class AddToCartRenderer
 *
 * @package Plumrocket\Amp\Block\Catalog\Product\ListForm
 * @method getProduct()
 * @method setProduct($product)
 */
class AddToCartRenderer extends \Magento\Framework\View\Element\Template implements
    ArgumentInterface,
    ButtonRendererInterface
{
    /**
     * @var string
     */
    private $stateName;

    /**
     * @var array
     */
    private $statePath = [];

    /**
     * @var string
     */
    private $formId;

    /**
     * @var \Plumrocket\Amp\ViewModel\GlobalState
     */
    private $stateModel;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Plumrocket\Amp\Model\State $stateModel,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->stateModel = $stateModel;
    }

    /**
     * @return mixed
     */
    public function getStateName()
    {
        return $this->stateName;
    }

    /**
     * @return array
     */
    public function getStatePath()
    {
        return $this->statePath;
    }

    /**
     * @return string
     */
    public function getStatePathString()
    {
        return implode('.', $this->statePath);
    }

    /**
     * @return string
     */
    public function getFormId()
    {
        return $this->formId;
    }

    /**
     * @return \Plumrocket\Amp\Model\State
     */
    public function getStateModel()
    {
        return $this->stateModel;
    }

    /**
     * Set default template
     *
     * @return string
     */
    protected function _toHtml() // @codingStandardsIgnoreLine
    {
        if (! $this->getTemplate()) {
            $this->setTemplate('Plumrocket_Amp::catalog/product/list/renderer/add_to_cart.phtml');
        }

        return parent::_toHtml();
    }

    /**
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     * @return string
     */
    public function renderByProduct($product)
    {
        $this->setProduct($product);
        $html = $this->toHtml();
        $this->setProduct(null);

        return $html;
    }

    /**
     * @param string $stateName
     * @return $this|ButtonRendererInterface
     */
    public function setStateName($stateName)
    {
        $this->stateName = $stateName;

        return $this;
    }

    /**
     * @param array $statePath
     * @return $this|ButtonRendererInterface
     */
    public function setProductIdPath(array $statePath)
    {
        $this->statePath = $statePath;

        return $this;
    }

    /**
     * @param string $formId
     * @return $this|ButtonRendererInterface
     */
    public function setFormId($formId)
    {
        $this->formId = $formId;

        return $this;
    }

    public function getProductId()
    {
        $product = $this->getProduct();
        return $product ? $product->getId() : null;
    }

    public function getPrice()
    {
        $product = $this->getProduct();
        return $product ? $product->getFinalPrice() : null;
    }
}
