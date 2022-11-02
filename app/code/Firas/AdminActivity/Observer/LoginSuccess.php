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
namespace Firas\AdminActivity\Observer;

use Magento\Framework\Event\ObserverInterface;
use \Firas\AdminActivity\Helper\Data as Helper;

/**
 * Class LoginSuccess
 * @package Firas\AdminActivity\Observer
 */
class LoginSuccess implements ObserverInterface
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
     * LoginSuccess constructor.
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
     * Login success
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $this->benchmark->start(__METHOD__);
        if (!$this->helper->isLoginEnable()) {
            return $observer;
        }
        
        $this->loginRepository
            ->setUser($observer->getUser())
            ->addSuccessLog();
        $this->benchmark->end(__METHOD__);
    }
}
