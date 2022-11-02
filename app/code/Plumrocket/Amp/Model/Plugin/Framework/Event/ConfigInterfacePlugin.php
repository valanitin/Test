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

namespace Plumrocket\Amp\Model\Plugin\Framework\Event;

use Magento\Framework\Event\ConfigInterface;

class ConfigInterfacePlugin
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @deprecated since 2.8.2 - use disabledObservers instead
     *
     * @var array
     */
    protected $_disabledObservers;

    /**
     * List of observers that need to be disabled
     *
     * @var array
     */
    private $disabledObservers;

    /**
     * @var array
     */
    private $additionalObserversClasses;

    /**
     * ConfigInterfacePlugin constructor.
     *
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param array $additionalObserversClasses
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        array $additionalObserversClasses = []
    ) {
        $this->objectManager = $objectManager;
        $this->additionalObserversClasses = $additionalObserversClasses;
    }

    /**
     * Add amp parameter for each url
     * @param  ConfigInterface $subject
     * @param  array $result
     * @return array
     */
    public function afterGetObservers(ConfigInterface $subject, $result) : array //@codingStandardsIgnoreLine
    {
        if (PHP_SAPI === 'cli' || empty($result)) {
            return $result;
        }

        /**
         * Need to use object manager to omit issues during setup:static-content:deploy
         * @var \Plumrocket\Amp\Helper\Data $dataHelper
         */
        $dataHelper = $this->objectManager->get(\Plumrocket\Amp\Helper\Data::class); //@codingStandardsIgnoreLine

        if ($dataHelper->isAmpRequest()) {
            foreach ($result as $key => $item) {
                if (isset($item['instance']) && ! $this->isAllowedObserver($item['instance'])) {
                    $result[$key]['disabled'] = true;
                }
            }
        }

        return $result;
    }

    /**
     * @deprecated since 2.8.2 - for extending, use DI with constructor argument $additionalObserversClasses
     *
     * @param  string $instance
     * @return boolean
     */
    protected function _isAllowedObserver($instance) //@codingStandardsIgnoreLine
    {
        return $this->isAllowedObserver($instance);
    }

    /**
     * Check of observer instance by list of disabled observers
     *
     * @param  string $instance
     * @return boolean
     */
    private function isAllowedObserver($instance) : bool
    {
        if ($instance) {
            return ! in_array($instance, $this->getDisabledObservers(), true);
        }

        return true;
    }

    /**
     * @return array
     */
    private function getDisabledObservers() : array
    {
        if (null === $this->disabledObservers) {
            $this->disabledObservers = array_merge(
                [
                    'Mageplaza\Seo\Observer\GenerateBlocksAfterObserver',
                    'Mageplaza\Seo\Observer\Markup',
                    'Mageplaza\Seo\Observer\MetaCmsObserver',
                    'Mageplaza\Seo\Observer\StoreForm',
                    'Mirasvit\Seo\Observer\Snippet',
                    'MageWorx\SeoExtended\Observer\ModifyMeta',
                    'Meetanshi\DeferJS\Model\Observer',
                    'Bss\DeferJS\Model\Observer\Defer',
                ],
                $this->additionalObserversClasses
            );
        }

        return $this->disabledObservers;
    }
}
