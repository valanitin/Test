<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2015 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

namespace Plumrocket\SocialLoginFree\Model\Config\Source;

use Magento\Cms\Model\Page as CmsPage;
use Plumrocket\SocialLoginFree\Helper\Data;

class Shareurl implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var null | array[]
     */
    protected $_options = null;

    /**
     * @var CmsPage
     */
    protected $cmsPage;

    /**
     * @var \Plumrocket\SocialLoginFree\Helper\Data
     */
    protected $dataHelper;

    /**
     * Shareurl constructor.
     *
     * @param \Magento\Cms\Model\Page                 $cmsPage
     * @param \Plumrocket\SocialLoginFree\Helper\Data $dataHelper
     */
    public function __construct(
        CmsPage $cmsPage,
        Data $dataHelper
    ) {
        $this->cmsPage      = $cmsPage;
        $this->dataHelper   = $dataHelper;
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
            $invitationsEnabled = $this->dataHelper->moduleInvitationsEnabled();

            $options = [
                ['value' => '__custom__',  'label' => __('Redirect to Custom URL')],
                [
                    'value' => '__invitations'. (!$invitationsEnabled? 'off' : '') .'__', 'disabled' => 'disabled',
                    'label' => __('Plumrocket Invitations Promo Page'. (!$invitationsEnabled? ' (Not installed)' : ''))
                ],
                ['value' => '__none__',    'label' => __('---')],
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
