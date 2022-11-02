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
 * Class CreditmemoService.
 */
class CreditmemoService
{
    public function afterRefund(\Magento\Sales\Model\Service\CreditmemoService $subject, $result)
    {
        $haveGiftCardItems = 0;
        $haveProductForRefund = 0;
        foreach ($result->getOrder()->getAllItems() as $items) {
            if ($haveGiftCardItems == 0) {
                if ($items->getProductType() == "giftcard") {
                    $haveGiftCardItems = 1;
                    continue;
                }
            }
            if (!($items->getQtyOrdered() == $items->getQtyRefunded())) {
                $haveProductForRefund = 1;
            }
        }
        if ($haveGiftCardItems==1 && $haveProductForRefund == 0) {
            $result->getOrder()->setStatus('closed');
            $result->getOrder()->setState('closed');
            $result->getOrder()->save();
        }
        return $result ;
    }
}
