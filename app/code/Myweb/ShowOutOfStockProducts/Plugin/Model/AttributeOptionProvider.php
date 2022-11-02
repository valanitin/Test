<?php


namespace Myweb\ShowOutOfStockProducts\Plugin\Model;

class AttributeOptionProvider
{

    /**
     * {@inheritdoc}
     * @return array $result
     */
    public function afterGetAttributeOptions(
        \Magento\ConfigurableProduct\Model\AttributeOptionProvider $subject,
        array $result
    ) {
        foreach ($result as &$option) {

            if (isset($option['stock_status']) && $option['stock_status']==0) {
                $option['option_title']  = $option['option_title'].__(' - Out of stock');
            }

        }
        return $result;
    }
}
