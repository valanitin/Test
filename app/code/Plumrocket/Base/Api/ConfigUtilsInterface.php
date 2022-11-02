<?php
/**
 * @package     Plumrocket_Base
 * @copyright   Copyright (c) 2022 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

namespace Plumrocket\Base\Api;

/**
 * @since 2.6.0
 */
interface ConfigUtilsInterface
{

    /**
     * Receive magento config value by store
     *
     * @param string     $path  full path, eg: "pr_base/general/enabled"
     * @param string|int $store store view code or id
     * @return mixed
     */
    public function getStoreConfig(string $path, $store = null);

    /**
     * Is flag set.
     *
     * @param string $path
     * @param null   $scopeCode
     * @param null   $scopeType
     * @return bool
     */
    public function isSetFlag(string $path, $scopeCode = null, $scopeType = null): bool;

    /**
     * Receive magento config value by store or by other scope type
     *
     * @param string      $path      full path, eg: "pr_base/general/enabled"
     * @param string|int  $scopeCode store view code or website code
     * @param string|null $scopeType
     * @return mixed
     */
    public function getConfig(string $path, $scopeCode = null, $scopeType = null);

    /**
     * Convert multiline text into array.
     *
     * @param string $fieldValue
     * @return array
     */
    public function splitTextareaValueByLine(string $fieldValue): array;

    /**
     * Convert multiselect value into array.
     *
     * @param string $value
     * @param bool   $clearEmpty
     * @return array
     */
    public function prepareMultiselectValue(string $value, bool $clearEmpty = true): array;
}
