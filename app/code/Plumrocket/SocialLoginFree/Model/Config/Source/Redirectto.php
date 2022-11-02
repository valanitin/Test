<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2015 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

namespace Plumrocket\SocialLoginFree\Model\Config\Source;

class Redirectto implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var null | array[]
     */
    protected $_options = null;

    /**
     * @var \Magento\Cms\Model\Page
     */
    protected $cmsPage;

    /**
     * Redirectto constructor.
     *
     * @param \Magento\Cms\Model\Page $cmsPage
     */
    public function __construct(\Magento\Cms\Model\Page $cmsPage)
    {
        $this->cmsPage = $cmsPage;
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_getOptions();
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        $options = [];
        foreach ($this->_getOptions() as $option) {
            $options[ $option['value'] ] = $option['label'];
        }

        return $options;
    }

    protected function _getOptions()
    {
        if (null === $this->_options) {
            $options = [
                ['value' => '__referer__',     'label' => __('Stay on the current page')],
                ['value' => '__custom__',      'label' => __('Redirect to Custom URL')],
                ['value' => '__none__',        'label' => __('---')],
                ['value' => '__dashboard__',   'label' => __('Customer -> Account Dashboard')],
            ];

            $items = $this->cmsPage->getCollection()->getItems();

            foreach ($items as $item) {
                if ($item->getId() == 1) {
                    continue;
                }
                $options[] = ['value' => $item->getId(), 'label' => $item->getTitle()];
            }

            $this->_options = $options;
        }

        return $this->_options;
    }
}
