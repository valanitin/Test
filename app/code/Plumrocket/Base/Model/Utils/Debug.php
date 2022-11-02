<?php
/**
 * @package     Plumrocket_Base
 * @copyright   Copyright (c) 2022 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\Base\Model\Utils;

/**
 * @since 2.6.0
 */
class Debug
{

    /**
     * Get backtrace as a table
     *
     * @param  string $title
     * @return string
     * phpcs:disable Magento2.Functions.StaticFunction
     */
    public static function backtrace(string $title = 'Debug Backtrace:'): string
    {
        $rows = '';
        foreach (debug_backtrace() as $stack) {
            if (! isset($stack['file'])) {
                $stack['file'] = '[PHP Kernel]';
            }
            if (! isset($stack['line'])) {
                $stack['line'] = '';
            }
            $rows .= '<tr>' .
                "<td>{$stack['file']}</td>" .
                "<td>{$stack['line']}</td>".
                "<td>{$stack['function']}</td>" .
                '</tr>';
        }

        return <<<HTML
<hr>
<style>
#pr_debug_bactrace,
#pr_debug_bactrace th,
#pr_debug_bactrace td {
  border: 1px solid black;
  border-collapse: collapse;
  padding: 4px;
}
#pr_debug_bactrace tr:nth-child(2n) {
  background-color: #e5e5e5;
}
</style>
<p>$title</p>
<table id="pr_debug_bactrace">
    <tr>
        <th><strong>File</strong></th>
        <th><strong>Line</strong></th>
        <th><strong>Function</strong></th>
    </tr>
    $rows
</table>
<hr>
HTML;
    }
}
