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
namespace Bss\SocialLogin\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;

/**
 * Class Action
 *
 * @package Bss\SocialLogin\Ui\Component\Listing\Column
 */
class Action extends \Bss\SocialLogin\Ui\Component\Listing\Column\AbstractColumn
{
    /**
     * @var \Magento\Backend\Helper\Data
     */
    protected $helper;

    /**
     * Action constructor.
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param \Magento\Backend\Helper\Data $helper
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        \Magento\Backend\Helper\Data $helper,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->helper = $helper;
    }

    /**
     * @param array $item
     * @return array|mixed
     */
    protected function _prepareItem(array & $item)
    {
        if (isset($item['customer_id'])) {
            $item[$this->getData('name')] = '<a href="'.$this->helper->getUrl('customer/*/edit')
                .'id/'.$item['customer_id'].'" class="view-customer" target="_blank">'.__('View Customer').'</a>';
        }

        return $item;
    }
}
