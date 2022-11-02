<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2019 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

namespace Plumrocket\SocialLoginFree\Model\Buttons;

class Preparer
{
    /**
     * @param array $buttons
     * @param array $sortableParams
     * @param bool  $splitByVisibility
     * @return array
     */
    public function prepareSortAndVisibility(array $buttons, array $sortableParams, $splitByVisibility = true)
    {
        $buttons = array_map(
            static function ($button) {
                $button['visible'] = true;
                return $button;
            },
            $buttons
        );

        $sortedButtons = [];
        if (isset($sortableParams['visible'])) {
            foreach ($sortableParams['visible'] as $button) {
                if (isset($buttons[$button])) {
                    $sortedButtons[$button] = $buttons[$button];
                    unset($buttons[$button]);
                }
            }
        } else {
            $sortedButtons = $buttons;
        }

        if (! $splitByVisibility) {
            return $sortedButtons;
        }

        return ['visible' => $sortedButtons, 'hidden' => []];
    }
}
