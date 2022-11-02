<?php
/**
 * Mage Tracer.
 *
 * @category   Magetracer
 * @package    Magetracer_StoreOptimization
 * @author     Magetracer
 * @copyright  Copyright (c) Mage Tracer (https://www.magetracer.com)
 * @license    https://www.magetracer.com/license.html
 */

namespace Magetracer\StoreOptimization\Logger;

class Handler extends \Magento\Framework\Logger\Handler\Base
{
    /**
     * Logging level
     * @var int
     */
    protected $loggerType = Logger::INFO;

    /**
     * File name
     * @var string
     */
    protected $fileName = '/var/log/optimizer.log';
}
