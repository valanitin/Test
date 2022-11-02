<?php
/**
 * Plumrocket Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End-user License Agreement
 * that is available through the world-wide-web at this URL:
 * http://wiki.plumrocket.net/wiki/EULA
 * If you are unable to obtain it through the world-wide-web, please
 * send an email to support@plumrocket.com so we can send you a copy immediately.
 *
 * @package     Plumrocket Amp v2.x.x
 * @copyright   Copyright (c) 2017 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Amp\Model\Attribute\Source;

class Mode extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    const DM_DEFAULT = 'DEFAULT';
    const DM_PAGE = 'PAGE';
    const DM_PRODUCT = 'PRODUCTS';
    const DM_MIXED = 'PRODUCTS_AND_PAGE';

    /**
     * Amp Display Mode options
     *
     * @return array
     */
    public function getAllOptions()
    {
        $options = [
            ['value' => self::DM_DEFAULT, 'label' => __('Default Mode')],
            ['value' => self::DM_PRODUCT, 'label' => __('Products only')],
            ['value' => self::DM_PAGE, 'label' => __('Static block only')],
            ['value' => self::DM_MIXED, 'label' => __('Static block and products')]
        ];

        return $options;
    }
}
