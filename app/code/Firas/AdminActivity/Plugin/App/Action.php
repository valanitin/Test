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
namespace Firas\AdminActivity\Plugin\App;

/**
 * Class Action
 * @package Firas\AdminActivity\Plugin\App
 */
class Action
{
    /**
     * @var \Firas\AdminActivity\Model\Processor
     */
    public $processor;

    /**
     * @var \Firas\AdminActivity\Helper\Benchmark
     */
    public $benchmark;

    /**
     * Action constructor.
     * @param \Firas\AdminActivity\Model\Processor $processor
     * @param \Firas\AdminActivity\Helper\Benchmark $benchmark
     */
    public function __construct(
        \Firas\AdminActivity\Model\Processor $processor,
        \Firas\AdminActivity\Helper\Benchmark $benchmark
    ) {
        $this->processor = $processor;
        $this->benchmark = $benchmark;
    }

    /**
     * Get before dispatch data
     * @param \Magento\Framework\Interception\InterceptorInterface $controller
     * @return void
     */
    public function beforeDispatch(\Magento\Framework\Interception\InterceptorInterface $controller)
    {
        $this->benchmark->start(__METHOD__);
        $actionName = $controller->getRequest()->getActionName();
        $fullActionName = $controller->getRequest()->getFullActionName();

        $this->processor->init($fullActionName, $actionName);
        $this->processor->addPageVisitLog($controller->getRequest()->getModuleName());
        $this->benchmark->end(__METHOD__);
    }
}
