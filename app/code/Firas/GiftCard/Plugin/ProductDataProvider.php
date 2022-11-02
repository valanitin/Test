<?php
/**
 * @Author      Firas Developers
 * @package     Firas_GiftCard
 * @copyright   Copyright (c) 2019 MAGETOP (https://www.firas.com)
 * @terms       https://www.firas.com/terms
 * @license     https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 **/
namespace Firas\GiftCard\Plugin;

/**
 * Class ProductDataProvider.
 */
class ProductDataProvider
{
    public function afterGetMeta(\Magento\Catalog\Ui\DataProvider\Product\Form\ProductDataProvider $subject, $result)
    {
        $productType = @$result['add_attribute_modal']['children']['create_new_attribute_modal']['children']['product_attribute_add_form']['arguments']['data']['config']['productType'];
        if ($productType == 'giftcard') {
            unset($result['custom_options']);
            return $result;
        } else {
            return $result;
        }
    }
}
