<?php
/**
 * @Author      Firas Developers
 * @package     Firas_GiftCard
 * @copyright   Copyright (c) 2019 MAGETOP (https://www.firas.com)
 * @terms       https://www.firas.com/terms
 * @license     https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 **/
namespace Firas\GiftCard\Block\Adminhtml\ProductType;

class Product extends \Magento\Catalog\Block\Adminhtml\Product
{
    /**
     * Retrieve options for 'Add Product' split button
     *
     * @return array
     */
    protected function _getAddProductButtonOptions()
    {
        $splitButtonOptions = [];
        $types = $this->_typeFactory->create()->getTypes();
        uasort(
            $types,
            function ($elementOne, $elementTwo) {
                return ($elementOne['sort_order'] < $elementTwo['sort_order']) ? -1 : 1;
            }
        );
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $moduleManager = $objectManager->create('Magento\Framework\Module\Manager');
        $giftCard = $moduleManager->isOutputEnabled('Firas_GiftCard');
        foreach ($types as $typeId => $type) {
            if ($typeId == 'giftcard' && $giftCard == 0) {
                continue;
            }
            $splitButtonOptions[$typeId] = [
                'label' => __($type['label']),
                'onclick' => "setLocation('" . $this->_getProductCreateUrl($typeId) . "')",
                'default' => \Magento\Catalog\Model\Product\Type::DEFAULT_TYPE == $typeId,
            ];
        }
        return $splitButtonOptions;
    }
}
