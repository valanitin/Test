<?php

namespace Meetanshi\SavedCards\Block;

use Magento\Payment\Block\ConfigurableInfo;

/**
 * Class Info
 * @package Meetanshi\SavedCards\Block
 */
class Info extends ConfigurableInfo
{
    /**
     * @var string
     */
    protected $_template = 'Meetanshi_SavedCards::payment/info.phtml';

    /**
     * @param string $field
     * @return \Magento\Framework\Phrase|string
     */
    public function getLabel($field)
    {
        switch ($field) {
            case 'card_holder_name':
                return __('Card Holder Name');
            case 'cc_type':
                return __('Card Type');
            case 'cc_last_4':
                return __('Mask Number');
            case 'card_expiry_date':
                return __('Expiration Date');
            case 'card_number':
                return __('Card number');
            case 'cc_cvv':
                return __('CVC/CVV2');
            default:
                return parent::getLabel($field);
        }
    }
}
