<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\SocialLoginFree\Model\Config\Source;

/**
 * @since 3.0.0
 */
class Position implements \Magento\Framework\Option\ArrayInterface
{
    const BUTTONS_POSITION_LOGIN = 'login';
    const BUTTONS_POSITION_REGISTER = 'register';
    const BUTTONS_POSITION_CHECKOUT = 'checkout';

    /**
     * @var null | array[]
     */
    protected $_options = null;

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
    public function toArray(): array
    {
        $options = [];
        foreach ($this->_getOptions() as $option) {
            $options[ $option['value'] ] = $option['label'];
        }

        return $options;
    }

    /**
     * @return array|array[]|null
     */
    protected function _getOptions()
    {
        if (null === $this->_options) {
            $options = [
                ['value' => self::BUTTONS_POSITION_LOGIN, 'label' => __('Login Form')],
                ['value' => self::BUTTONS_POSITION_REGISTER, 'label' => __('Registration Form')],
                ['value' => self::BUTTONS_POSITION_CHECKOUT, 'label' => __('Checkout Page')],
            ];

            $this->_options = $options;
        }

        return $this->_options;
    }
}
