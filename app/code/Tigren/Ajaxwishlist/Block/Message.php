<?php
/**
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Tigren\Ajaxwishlist\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Tigren\Ajaxsuite\Helper\Data;

/**
 * Class Message
 *
 * @package Tigren\Ajaxwishlist\Block
 */
class Message extends Template
{
    /**
     * @var Data
     */
    protected $_ajaxsuiteHelper;

    /**
     * Message constructor.
     *
     * @param Context $context
     * @param Data    $ajaxsuiteHelper
     * @param array   $data
     */
    public function __construct(
        Context $context,
        Data $ajaxsuiteHelper,
        array $data
    ) {
        parent::__construct($context, $data);
        $this->_ajaxsuiteHelper = $ajaxsuiteHelper;
    }

    /**
     * @return mixed|string
     */
    public function getMessage()
    {
        $message = $this->_ajaxsuiteHelper->getScopeConfig('ajaxwishlist/general/message');
        if (!$message) {
            $message = __('You have added this product to your wishlist');
        }
        return $message;
    }
}
