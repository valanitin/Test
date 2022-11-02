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
namespace Firas\AdminActivity\Plugin\User;

/**
 * Class Delete
 * @package Firas\AdminActivity\Plugin\User
 */
class Delete
{
    /**
     * @var \Firas\AdminActivity\Helper\Benchmark
     */
    public $benchmark;

    /**
     * Delete constructor.
     * @param \Firas\AdminActivity\Helper\Benchmark $benchmark
     */
    public function __construct(
        \Firas\AdminActivity\Helper\Benchmark $benchmark
    ) {
        $this->benchmark = $benchmark;
    }
    /**
     * @param \Magento\User\Model\ResourceModel\User $user
     * @param callable $proceed
     * @param $object
     * @return mixed
     */
    public function aroundDelete(\Magento\User\Model\ResourceModel\User $user, callable $proceed, $object)
    {
        $this->benchmark->start(__METHOD__);
        $object->load($object->getId());

        $result = $proceed($object);
        $object->afterDelete();

        $this->benchmark->end(__METHOD__);
        return $result;
    }
}
