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
namespace Bss\SocialLogin\Model\Config\Source;

/**
 * Class SocialType
 * @package Bss\SocialLogin\Model\Config\Source
 */
class SocialType implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'facebook', 'label' =>__('Facebook')],
            ['value' => 'googleplus', 'label' =>__('Googleplus')],
            ['value' => 'instagram', 'label' =>__('Instagram')],
            ['value' => 'linkedin', 'label' =>__('Linkedin')],
            ['value' => 'live', 'label' =>__('Live')],
            ['value' => 'pinterest', 'label' =>__('Pinterest')],
            ['value' => 'twitter', 'label' =>__('Twitter')],
            ['value' => 'vkontakte', 'label' =>__('Vkontakte')],
            ['value' => 'yahoo', 'label' =>__('Yahoo')]
        ];
    }
}
