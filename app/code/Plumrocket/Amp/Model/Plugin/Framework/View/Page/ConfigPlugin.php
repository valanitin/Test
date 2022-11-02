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
 * @copyright   Copyright (c) 2016 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Amp\Model\Plugin\Framework\View\Page;

use Magento\Framework\View\Page\Config;
use Magento\Framework\View\Asset\GroupedCollection;

use Plumrocket\Amp\Block\Page\Head\Og\AbstractOg as AbstractOg;

/**
 * Plugin for processing builtin cache
 */
class ConfigPlugin
{
    /**
     * @var \Plumrocket\Amp\Helper\Data
     */
    protected $_dataHelper;

    /**
     * @var \Plumrocket\Amp\Helper\Google\Analytics
     */
    private $analyticsHelper;

    /**
     * ConfigPlugin constructor.
     *
     * @param \Plumrocket\Amp\Helper\Data             $dataHelper
     * @param \Plumrocket\Amp\Helper\Google\Analytics $analyticsHelper
     */
    public function __construct(
        \Plumrocket\Amp\Helper\Data $dataHelper,
        \Plumrocket\Amp\Helper\Google\Analytics $analyticsHelper
    ) {
        $this->_dataHelper = $dataHelper;
        $this->analyticsHelper = $analyticsHelper;
    }

    /**
     * @param  \Magento\Framework\View\Page\Config
     * @param  string $result
     * @return string $result
     */
    public function afterGetIncludes(Config $subject, $result)
    {
        if (!$this->_dataHelper->isAmpRequest()) {
            return $result;
        }

        return '';
    }

    /**
     * @param  \Magento\Framework\View\Page\Config
     * @param  \Magento\Framework\View\Asset\GroupedCollection $result
     * @return \Magento\Framework\View\Asset\GroupedCollection $result
     */
    public function afterGetAssetCollection(Config $subject, $result)
    {
        if (!$this->_dataHelper->isAmpRequest()) {
            return $result;
        }

        $canonicalUrl = null;

        foreach ($result->getGroups() as $group) {
            $type = $group->getProperty(GroupedCollection::PROPERTY_CONTENT_TYPE);

            if ('ico' !== $type) {
                foreach ($group->getAll() as $identifier => $asset) {

                    if ('canonical' === $type
                        || (isset($group->getProperty('attributes')['rel'])
                        && 'canonical' === $group->getProperty('attributes')['rel'])
                    ) {
                        $canonicalUrl = $asset->getUrl();
                    }

                    if (!in_array($identifier, ['icon', 'shortcut-icon'])) {
                        $result->remove($identifier);
                    }
                }
            }
        }

        $subject->addRemotePageAsset(
            $this->_dataHelper->getCanonicalUrl($canonicalUrl),
            'canonical',
            ['attributes' => ['rel' => 'canonical']],
            AbstractOg::DEFAULT_ASSET_NAME
        );

        return $result;
    }

    /**
     * @param Config   $subject
     * @param \Closure $proceed
     * @param          $elementType
     * @return array|mixed
     */
    public function aroundGetElementAttributes(Config $subject, \Closure $proceed, $elementType)
    {
        /**
         * Get result by original method
         */
        $result = $proceed($elementType);

        /**
         * Add attributes in tags by $elementType
         * (Only for amp request)
         */
        if ($this->_dataHelper->isAmpRequest()) {
            switch (strtolower($elementType)) {
                case \Magento\Framework\View\Page\Config::ELEMENT_TYPE_HTML:
                    $result['amp'] = '';
                    break;
                case \Magento\Framework\View\Page\Config::ELEMENT_TYPE_BODY:
                    $result = array_diff_key($result, array_count_values(['itemtype', 'itemscope', 'itemprop']));
                    if ($this->_dataHelper->getRtlEnabled()) {
                        $rtlClass = ' dir-rtl';
                        if (!array_key_exists('class', $result)) {
                            $result['class'] = '';
                        }

                        $result['class'] = $result['class'] . $rtlClass;
                    }
                    break;
                default:
                    break;
            }
        }

        return $result;
    }

    /**
     * Set meta for googleanalytics
     * (Only for amp request)
     *
     * @param Config $subject
     */
//    public function beforeGetMetadata(Config $subject)
//    {
//        if ($this->_dataHelper->isAmpRequest() && $this->analyticsHelper->isGoogleAnalyticsAvailable()) {
//            $subject->setMetadata('amp-google-client-id-api', 'googleanalytics');
//        }
//    }
}
