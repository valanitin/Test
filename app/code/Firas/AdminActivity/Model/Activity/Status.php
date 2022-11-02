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
namespace Firas\AdminActivity\Model\Activity;

use \Magento\Framework\Model\AbstractModel;

/**
 * Class Status
 * @package Firas\AdminActivity\Model\Activity
 */
class Status extends AbstractModel
{
    /**
     * @var Int
     */
    const ACTIVITY_NONE = 0;

    /**
     * @var Int
     */
    const ACTIVITY_REVERTABLE = 1;

    /**
     * @var Int
     */
    const ACTIVITY_REVERT_SUCCESS = 2;

    /**
     * @var Int
     */
    const ACTIVITY_FAIL = 3;

    /**
     * @var \Firas\AdminActivity\Model\ActivityFactory
     */
    public $activityFactory;

    /**
     * Status constructor.
     * @param \Firas\AdminActivity\Model\ActivityFactory $activityFactory
     */
    public function __construct(
        \Firas\AdminActivity\Model\ActivityFactory $activityFactory
    ) {
        $this->activityFactory = $activityFactory;
    }

    /**
     * Set success revert status
     * @param $activityId
     * @return void
     */
    public function markSuccess($activityId)
    {
        $activityModel = $this->activityFactory->create()->load($activityId);
        $activityModel->setIsRevertable(self::ACTIVITY_REVERT_SUCCESS);
        $activityModel->save();
    }

    /**
     * Set fail revert status
     * @param $activityId
     * @return void
     */
    public function markFail($activityId)
    {
        $activityModel = $this->activityFactory->create()->load($activityId);
        $activityModel->setIsRevertable(self::ACTIVITY_FAIL);
        $activityModel->save();
    }
}
