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
namespace Firas\AdminActivity\Ui\Component\Listing\Column\ActionType;

/**
 * Class Options
 * @package Firas\AdminActivity\Ui\Component\Listing\Column\ActionType
 */
class Options implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \Firas\AdminActivity\Helper\Data
     */
    public $helper;

    /**
     * Options constructor.
     * @param \Firas\AdminActivity\Helper\Data $helper
     */
    public function __construct(\Firas\AdminActivity\Helper\Data $helper)
    {
        $this->helper = $helper;
    }

    /**
     * List all option to get in filter
     * @return array
     */
    public function toOptionArray()
    {
        $data = [];
        $lableList = $this->helper->getAllActions();
        foreach ($lableList as $key => $value) {
            $data[] = ['value'=> $key,'label'=> __($value)];
        }
        return $data;
    }
}
