<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Meta
 */


namespace Amasty\Meta\Controller\Adminhtml\Custom;

class Edit extends \Amasty\Meta\Controller\Adminhtml\Config\Edit
{
    /**
     * @var string
     */
    protected $paramName = 'id';

    /**
     * @var string
     */
    protected $_blockName = 'Custom';
}
