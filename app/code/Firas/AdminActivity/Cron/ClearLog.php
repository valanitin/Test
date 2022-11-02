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
namespace Firas\AdminActivity\Cron;

use Psr\Log\LoggerInterface;
use Firas\AdminActivity\Helper\Data as Helper;
use Firas\AdminActivity\Api\ActivityRepositoryInterface;

/**
 * Class ClearLog
 * @package Firas\AdminActivity\Cron
 */
class ClearLog
{
    /**
     * Default date format
     * @var string
     */
    const DATE_FORMAT = 'Y-m-d H:i:s';

    /**
     * @var LoggerInterface
     */
    public $logger;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    public $dateTime;

    /**
     * @var \Firas\AdminActivity\Helper\Data
     */
    public $helper;

    /**
     * @var ActivityRepositoryInterface
     */
    public $activityRepository;

    /**
     * @var \Firas\AdminActivity\Api\LoginRepositoryInterface
     */
    public $loginRepository;

    /**
     * ClearLog constructor.
     * @param LoggerInterface $logger
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $dateTime
     * @param Helper $helper
     * @param ActivityRepositoryInterface $activityRepository
     * @param \Firas\AdminActivity\Api\LoginRepositoryInterface $loginRepository
     */
    public function __construct(
        LoggerInterface $logger,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        Helper $helper,
        ActivityRepositoryInterface $activityRepository,
        \Firas\AdminActivity\Api\LoginRepositoryInterface $loginRepository
    ) {
        $this->logger = $logger;
        $this->dateTime = $dateTime;
        $this->helper = $helper;
        $this->activityRepository = $activityRepository;
        $this->loginRepository = $loginRepository;
    }

    /**
     * Return cron cleanup date
     * @return null|string
     */
    public function __getDate()
    {
        $timestamp = $this->dateTime->gmtTimestamp();
        $day = $this->helper->getConfigValue('CLEAR_LOG_DAYS');
        if ($day) {
            $timestamp -= $day * 24 * 60 * 60;
            return $this->dateTime->gmtDate(self::DATE_FORMAT, $timestamp);
        }
        return null;
    }

    /**
     * Delete record which date is less than the current date
     * @return $this|null
     */
    public function execute()
    {
        try {
            if (!$this->helper->isEnable()) {
                return $this;
            }

            if ($date = $this->__getDate()) {
                $activities = $this->activityRepository->getListBeforeDate($date);
                if (!empty($activities)) {
                    foreach ($activities as $activity) {
                        //TODO: Remove activity detail
                        $activity->delete();
                    }
                }

                //TODO: Remove login activity detail
                if ($this->helper->isLoginEnable()) {
                    $activities = $this->loginRepository->getListBeforeDate($date);
                    if (!empty($activities)) {
                        foreach ($activities as $activity) {
                            $activity->delete();
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            $this->logger->debug($e->getMessage());
        }
        return null;
    }
}
