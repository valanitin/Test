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

/**
 * Class LoadAfter
 * @package Firas\AdminActivity\Observer
 */
class LoadAfter implements ObserverInterface
{
    /**
     * @var \Firas\AdminActivity\Model\Processor
     */
    private $processor;

    /**
     * @var \Firas\AdminActivity\Helper\Data
     */
    public $helper;

    /**
     * @var \Firas\AdminActivity\Helper\Benchmark
     */
    public $benchmark;

    /**
     * LoadAfter constructor.
     * @param \Firas\AdminActivity\Model\Processor $processor
     * @param \Firas\AdminActivity\Helper\Data $helper
     * @param \Firas\AdminActivity\Helper\Benchmark $benchmark
     */
    public function __construct(
        \Firas\AdminActivity\Model\Processor $processor,
        \Firas\AdminActivity\Helper\Data $helper,
        \Firas\AdminActivity\Helper\Benchmark $benchmark
    ) {
        $this->processor = $processor;
        $this->helper = $helper;
        $this->benchmark = $benchmark;
    }

    /**
     * Delete after
     * @param \Magento\Framework\Event\Observer $observer
     * @return \Magento\Framework\Event\Observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $this->benchmark->start(__METHOD__);
        if (!$this->helper->isEnable()) {
            return $observer;
        }
        $object = $observer->getEvent()->getObject();
        $this->processor->modelLoadAfter($object);
        $this->benchmark->end(__METHOD__);
    }
}
