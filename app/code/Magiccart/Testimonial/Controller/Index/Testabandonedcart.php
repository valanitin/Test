<?php
/**
 * Magiccart 
 * @category    Magiccart 
 * @copyright   Copyright (c) 2014 Magiccart (http://www.magiccart.net/) 
 * @license     http://www.magiccart.net/license-agreement.html
 * @Author: DOng NGuyen<nguyen@dvn.com>
 * @@Create Date: 2016-01-05 10:40:51
 * @@Modify Date: 2016-03-29 15:35:32
 * @@Function:
 */

namespace Magiccart\Testimonial\Controller\Index;

class Testabandonedcart extends \Magiccart\Testimonial\Controller\Index
{
    public function execute()
    {
        $cron = \Magento\Framework\App\ObjectManager::getInstance()
            ->create('Firas\Cronbrands\Abandonedcart\Abandonedcart');

        $cron->execute();
    }

}
