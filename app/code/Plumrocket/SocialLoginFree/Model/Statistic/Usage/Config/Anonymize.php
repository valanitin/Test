<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\SocialLoginFree\Model\Statistic\Usage\Config;

class Anonymize
{
    /**
     * @param $currentValue
     * @return string|mixed
     */
    public function cmsPageValue($currentValue)
    {
        if (is_numeric($currentValue)) {
            return '__cms_page__';
        }

        return $currentValue;
    }

    /**
     * @param array $currentOptions
     * @return array
     */
    public function cmsPageOptions(array $currentOptions) : array
    {
        return array_reduce(
            $currentOptions,
            static function ($newOptions, $option) {
                if (!is_numeric($option['value'])) {
                    $newOptions[] = $option;
                }
                return $newOptions;
            },
            [['value' => '__cms_page__', 'label' => 'Some CMS page']]
        );
    }
}
