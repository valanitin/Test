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
namespace Firas\AdminActivity\Api;

/**
 * Interface LoginRepositoryInterface
 * @package Firas\AdminActivity\Api
 */
interface LoginRepositoryInterface
{
    /**
     * Set login data
     * @param $status
     * @param $type
     * @return mixed
     */
    public function addLog($status, $type);

    /**
     * Get all admin activity data before date
     * @param $endDate
     * @return mixed
     */
    public function getListBeforeDate($endDate);

    /**
     * Set login user
     * @param $user
     * @return mixed
     */
    public function setUser($user);
}
