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

class Store extends \Magento\Store\Block\Switcher
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
     * @var \Magento\Framework\Locale\Resolver
     */
    private $localeResolver;

    /**
     * @var \Plumrocket\Amp\Block\Page\Flag|false
     */
    private $flagRenderer;

    /**
     * Part of the global state which used in this block
     *
     * @var array
     */
    private $statePath = ['store', 'target'];

    /**
     * Store constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Data\Helper\PostHelper        $postDataHelper
     * @param \Plumrocket\Amp\ViewModel\GlobalState            $globalState
     * @param \Plumrocket\Amp\Model\State                      $stateModel
     * @param \Magento\Framework\Locale\Resolver               $localeResolver
     * @param array                                            $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Data\Helper\PostHelper $postDataHelper,
        \Plumrocket\Amp\ViewModel\GlobalState $globalState,
        \Plumrocket\Amp\Model\State $stateModel,
        \Magento\Framework\Locale\Resolver $localeResolver,
        array $data = []
    ) {
        parent::__construct($context, $postDataHelper, $data);
        $this->globalState = $globalState;
        $this->stateModel = $stateModel;
        $this->localeResolver = $localeResolver;
    }

    /**
     * Action for switch store view
     *
     * @return string
     */
    public function getActionUrl() : string
    {
        return $this->getUrl('pramp/api_store/switch');
    }

    /**
     * @return array
     */
    public function getTargetStoreCodeStatePath() : array
    {
        return $this->statePath;
    }

    /**
     * @return \Magento\Store\Block\Switcher
     */
    protected function _beforeToHtml() : \Magento\Store\Block\Switcher //@codingStandardsIgnoreLine
    {
        $this->globalState->addStateProperty(
            $this->stateModel->createArrayByPath($this->getTargetStoreCodeStatePath(), $this->getCurrentStoreCode())
        );

        return parent::_beforeToHtml();
    }

    /**
     * @param $storeCode
     * @return string
     */
    public function getTargetSetJs($storeCode) : string
    {
        return $this->stateModel->createAmpJsSetter($this->getTargetStoreCodeStatePath(), $storeCode);
    }

    /**
     * @return string
     */
    public function getTargetGetterJs() : string
    {
        return $this->stateModel->createAmpJsGetter($this->getTargetStoreCodeStatePath());
    }

    /**
     * Get current store locale code.
     *
     * @return string
     */
    public function getCurrentStoreLocaleCode() : string
    {
        return $this->localeResolver->getLocale();
    }

    /**
     * @param $storeLocaleCode
     * @return string
     */
    public function getFlagImageHtml($storeLocaleCode) : string
    {
        $html = '';

        if ($this->getFlagRenderer()) {
            $html .= $this->getFlagRenderer()->setCode($storeLocaleCode)->render();
        }

        return $html;
    }

    /**
     * @return false|\Plumrocket\Amp\Block\Page\Flag
     */
    private function getFlagRenderer()
    {
        if (null === $this->flagRenderer) {
            $this->flagRenderer = $this->getChildBlock('flag_renderer');
            if (! $this->flagRenderer) {
                $this->_logger->debug('AMP - Renderer for Store Flag not defined');
            }
        }

        return $this->flagRenderer;
    }
}
