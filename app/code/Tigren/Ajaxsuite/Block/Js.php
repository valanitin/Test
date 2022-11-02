<?php
/**
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Tigren\Ajaxsuite\Block;

use Magento\Framework\Data\Form\FormKey;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Tigren\Ajaxsuite\Helper\Data;

/**
 * Class Js
 *
 * @package Tigren\Ajaxsuite\Block
 */
class Js extends Template
{
    /**
     * @var string
     */
    protected $_template = 'js/main.phtml';
    /**
     * @var Data
     */
    protected $_ajaxsuiteHelper;
    /**
     * @var FormKey
     */
    protected $formKey;

    /**
     * Js constructor.
     *
     * @param Context $context
     * @param FormKey $formKey
     * @param Data    $ajaxsuiteHelper
     * @param array   $data
     */
    public function __construct(
        Context $context,
        FormKey $formKey,
        Data $ajaxsuiteHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->formKey = $formKey;
        $this->_ajaxsuiteHelper = $ajaxsuiteHelper;
    }

    /**
     * @return string
     */
    public function getFormKey()
    {
        return $this->formKey->getFormKey();
    }

    /**
     * @return string
     */
    public function getAjaxLoginUrl()
    {
        return $this->getUrl('ajaxsuite/login');
    }

    /**
     * @return string
     */
    public function getAjaxWishlistUrl()
    {
        return $this->getUrl('ajaxsuite/wishlist');
    }

    /**
     * @return string
     */
    public function getAjaxCompareUrl()
    {
        return $this->getUrl('ajaxsuite/compare');
    }

    /**
     * @return string
     */
    public function getAjaxSuiteInitOptions()
    {
        return $this->_ajaxsuiteHelper->getAjaxSuiteInitOptions();
    }
}
