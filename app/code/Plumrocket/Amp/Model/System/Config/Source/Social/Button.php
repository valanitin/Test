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
 * @copyright   Copyright (c) 2017 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Amp\Model\System\Config\Source\Social;

class Button implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'email'     => __('Email'),
            'facebook'  => __('Facebook'),
            'linkedin'  => __('LinkedIn'),
            'pinterest' => __('Pinterest'),
            'tumblr'    => __('Tumblr'),
            'twitter'   => __('Twitter'),
            'whatsapp'  => __('WhatsApp'),
        ];
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $result = [];
        foreach ($this->toArray() as $value => $label) {
            $result[] = ['value' => $value, 'label' => $label ];
        }

        return $result;
    }
}
