<?php
/**
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Tigren\Ajaxwishlist\Block\Product;

use Magento\Framework\View\Element\Template;

/**
 * Class ConfigurableOption
 *
 * @package Tigren\Ajaxwishlist\Block\Product
 */
class ConfigurableOption extends Template
{

    /**
     * @return mixed
     */
    public function getColorLabel()
    {
        return $this->_request->getParam('colorLabel');
    }

    /**
     * @return mixed
     */
    public function getSizeLabel()
    {
        return $this->_request->getParam('sizeLabel');
    }
}
