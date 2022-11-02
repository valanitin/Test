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
namespace Firas\AdminActivity\Plugin;

use \Firas\AdminActivity\Helper\Data as Helper;

/**
 * Class Auth
 * @package Firas\AdminActivity\Plugin
 */
class Auth
{
    /**
     * @var Helper
     */
    public $helper;

    /**
     * @var \Firas\AdminActivity\Api\LoginRepositoryInterface
     */
    public $loginRepository;

    /**
     * @var \Firas\AdminActivity\Helper\Benchmark
     */
    public $benchmark;

    /**
     * Auth constructor.
     * @param Helper $helper
     * @param \Firas\AdminActivity\Api\LoginRepositoryInterface $loginRepository
     * @param \Firas\AdminActivity\Helper\Benchmark $benchmark
     */
    public function __construct(
        Helper $helper,
        \Firas\AdminActivity\Api\LoginRepositoryInterface $loginRepository,
        \Firas\AdminActivity\Helper\Benchmark $benchmark
    ) {
        $this->helper = $helper;
        $this->loginRepository = $loginRepository;
        $this->benchmark = $benchmark;
    }

    /**
     * Track admin logout activity
     * @param \Magento\Backend\Model\Auth $auth
     * @param callable $proceed
     * @return mixed
     */
    public function aroundLogout(\Magento\Backend\Model\Auth $auth, callable $proceed)
    {
        $this->benchmark->start(__METHOD__);
        if ($this->helper->isLoginEnable()) {
            $user = $auth->getAuthStorage()->getUser();
            $this->loginRepository->setUser($user)->addLogoutLog();
        }
        $result = $proceed();
        $this->benchmark->end(__METHOD__);
        return $result;
    }
}
