<?php
/**
 * Grid Logger Handler.
 * @category  Firas
 * @package   Firas_Grid
 * @author    Firas
 * @copyright Copyright (c) 2010-2017 Firas Software Private Limited (https://firas.com)
 * @license   https://store.firas.com/license.html
 */

namespace Firas\Grid\Logger;

class Handler extends \Magento\Framework\Logger\Handler\Base
{
    /**
     * Logging level.
     *
     * @var int
     */
    public $loggerType = Logger::INFO;

    /**
     * File name.
     *
     * @var string
     */
    public $fileName = '/var/log/grid.log';
}
