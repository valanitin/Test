<?php
/**
 * @package     Plumrocket_Base
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license/  End-user License Agreement
 */

namespace Plumrocket\Base\Api;

/**
 * Collect statistic for specific module
 * Must be added to composite collector for send
 * @see \Plumrocket\Base\Model\Statistic\Usage\CompositeCollector
 *
 * @since 2.3.0
 */
interface ModuleUsageStatisticCollectorInterface extends UsageStatisticCollectorInterface
{
    /**
     * Must return array
     *  [
     *      'config' => [
     *          'path' => [
     *              'label' => '',
     *              'value' => '',
     *              'options' => [
     *                  ['value' => value_1, 'label' => label_1],
     *                  ['value' => value_1, 'label' => label_1],
     *              ],
     *              'default' => ''
     *          ]
     *      ]
     *  ]
     *
     * @return array
     */
    public function collect(): array;

    /**
     * Check if something changed after last send
     *
     * @return bool
     */
    public function hasNewData(): bool;
}
