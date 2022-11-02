<?php
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * @category   BSS
 * @package    Bss_AjaxSocialLogin
 * @author     Extension Team
 * @copyright  Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */

namespace Bss\SocialLogin\Block\SocialLogin;

use Magento\Framework\View\Element\Template;

/**
 * Class EnableModule
 *
 * @package Bss\SocialLogin\Block\SocialLogin
 */
class EnableModule extends Template
{
    /**
     * @var \Bss\SocialLogin\Helper\Data
     */
    protected $helper;

    /**
     * EnableModule constructor.
     * @param Template\Context $context
     * @param \Bss\SocialLogin\Helper\Data $helper
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        \Bss\SocialLogin\Helper\Data $helper,
        array $data = []
    ) {
        $this->helper = $helper;
        parent::__construct($context, $data);
    }

    /**
     * @return bool
     */
    public function isEnable()
    {
        return $this->helper->moduleEnabled();
    }
}
