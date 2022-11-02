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

use Magento\Customer\Block\Form\Register;

/**
 * Class Create
 *
 * @package Bss\SocialLogin\Block\SocialLogin
 */
class Create extends Register
{
    /**
     * @return $this|Register
     */
    protected function _prepareLayout()
    {
        return $this;
    }
}
