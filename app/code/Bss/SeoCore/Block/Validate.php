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
 * @package    Bss_SeoCore
 * @author     Extension Team
 * @copyright  Copyright (c) 2018-2019 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\SeoCore\Block;

/**
 * Class Validate
 * @package Bss\SeoCore\Block
 */
class Validate extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Bss\SeoCore\Helper\Data
     */
    private $dataHelper;

    /**
     * Validate constructor.
     * @param \Bss\SeoCore\Helper\Data $dataHelper
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Bss\SeoCore\Helper\Data $dataHelper,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        $this->dataHelper = $dataHelper;
        parent::__construct($context, $data);
    }

    /**
     * @return \Bss\SeoCore\Helper\Data
     */
    public function getHelper()
    {
        return $this->dataHelper;
    }

}
