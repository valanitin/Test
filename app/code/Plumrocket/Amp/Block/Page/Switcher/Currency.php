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

namespace Plumrocket\Amp\Block\Page\Switcher;

class Currency extends \Magento\Directory\Block\Currency
{
    /**
     * @var \Plumrocket\Amp\ViewModel\GlobalState
     */
    private $globalState;

    /**
     * @var \Plumrocket\Amp\Model\State
     */
    private $stateModel;

    /**
     * Part of the global state which used in this block
     *
     * @var array
     */
    private $statePath = ['currency', 'target'];

    /**
     * Currency constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Directory\Model\CurrencyFactory         $currencyFactory
     * @param \Magento\Framework\Data\Helper\PostHelper        $postDataHelper
     * @param \Magento\Framework\Locale\ResolverInterface      $localeResolver
     * @param \Plumrocket\Amp\ViewModel\GlobalState            $globalState
     * @param \Plumrocket\Amp\Model\State                      $stateModel
     * @param array                                            $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Directory\Model\CurrencyFactory $currencyFactory,
        \Magento\Framework\Data\Helper\PostHelper $postDataHelper,
        \Magento\Framework\Locale\ResolverInterface $localeResolver,
        \Plumrocket\Amp\ViewModel\GlobalState $globalState,
        \Plumrocket\Amp\Model\State $stateModel,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $currencyFactory,
            $postDataHelper,
            $localeResolver,
            $data
        );
        $this->globalState = $globalState;
        $this->stateModel = $stateModel;
    }

    /**
     * Action for switch currency
     *
     * @return string
     */
    public function getActionUrl() : string
    {
        return $this->getUrl('pramp/api_currency/switch');
    }

    /**
     * @return string
     */
    public function getBackUrl() : string
    {
        return $this->_urlBuilder->getCurrentUrl();
    }

    /**
     * @return array
     */
    public function getNextCurrencyCodeStatePath() : array
    {
        return $this->statePath;
    }

    /**
     * @return Currency
     */
    protected function _beforeToHtml() //@codingStandardsIgnoreLine
    {
        $this->globalState->addStateProperty(
            $this->stateModel->createArrayByPath($this->getNextCurrencyCodeStatePath(), $this->getCurrentCurrencyCode())
        );

        return parent::_beforeToHtml();
    }

    /**
     * @param $storeCode
     * @return string
     */
    public function getTargetSetJs($storeCode) : string
    {
        return $this->stateModel->createAmpJsSetter($this->getNextCurrencyCodeStatePath(), $storeCode);
    }

    /**
     * @return string
     */
    public function getTargetGetterJs() : string
    {
        return $this->stateModel->createAmpJsGetter($this->getNextCurrencyCodeStatePath());
    }

    /**
     * @param $storeLocaleCode
     * @return string
     */
    public function getFlagImageHtml($storeLocaleCode) : string
    {
        /** @var \Plumrocket\Amp\Block\Page\Flag|false $renderer */
        $renderer = $this->getChildBlock('flag_renderer');

        $html = '';

        if ($renderer) {
            $html .= $renderer->setCode($storeLocaleCode)->render();
        } else {
            $this->_logger->debug('AMP:: Renderer for Store Flag not defined');
        }

        return $html;
    }
}
