<?php
/**
 * Firas
 *
 * Do not edit or add to this file if you wish to upgrade to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please contact us https://firas.co.uk/contacts.
 *
 * @category   Firas
 * @package    Firas_AdminActivity
 * @copyright  Copyright (C) 2018 Kiwi Commerce Ltd (https://firas.co.uk/)
 * @license    https://firas.co.uk/magento2-extension-license/
 */
namespace Firas\AdminActivity\Api\Activity;

/**
 * Interface ModelInterface
 * @package Firas\AdminActivity\Api\Activity
 */
interface ModelInterface
{
    /**
     * Get old data
     * @param $model
     * @return mixed
     */
    public function getOldData($model);

    /**
     * Get edit data
     * @param $model
     * @return mixed
     */
    public function getEditData($model, $fieldArray);
}
