<?php
/**
 * @Author      Firas Developers
 * @package     Firas_GiftCard
 * @copyright   Copyright (c) 2019 MAGETOP (https://www.firas.com)
 * @terms       https://www.firas.com/terms
 * @license     https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 **/
namespace Firas\GiftCard\Model;
 
use Magento\Framework\Model\AbstractModel;
 
class GiftUser extends \Magento\Framework\Model\AbstractModel
{
    protected function _construct()
    {
        $this->_init('Firas\GiftCard\Model\ResourceModel\GiftUser');
    }
}
