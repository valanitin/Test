<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_SeoToolKit
 */


declare(strict_types=1);

namespace Amasty\SeoToolKit\Model\Source;

class RedirectType implements \Magento\Framework\Data\OptionSourceInterface
{
    const REDIRECT_301 = 301;
    const REDIRECT_302 = 302;

    /**
     * @return array|array[]
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::REDIRECT_301, 'label' => __('301 Moved Permanently')],
            ['value' => self::REDIRECT_302, 'label' => __('302 Found')]
        ];
    }
}
