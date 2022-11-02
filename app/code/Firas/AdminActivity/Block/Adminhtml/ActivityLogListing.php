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
namespace Firas\AdminActivity\Block\Adminhtml;

use Magento\Backend\Block\Template;
use Firas\AdminActivity\Api\ActivityRepositoryInterface;
use Firas\AdminActivity\Helper\Browser;

/**
 * Class ActivityLogListing
 * @package Firas\AdminActivity\Block\Adminhtml
 */
class ActivityLogListing extends Template
{
    /**
     * @var ActivityRepositoryInterface
     */
    public $activityRepository;

    /**
     * @var Browser
     */
    public $browser;

    /**
     * Path to template file in theme.
     * @var string
     */
    public $_template = 'Firas_AdminActivity::log_listing.phtml';

    /**
     * ActivityLogListing constructor.
     * @param Template\Context $context
     * @param ActivityRepositoryInterface $activityRepository
     * @param Browser $browser
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        ActivityRepositoryInterface $activityRepository,
        Browser $browser
    ) {
        $this->activityRepository = $activityRepository;
        $this->browser = $browser;
        parent::__construct($context);
    }

    /**
     * Get admin activity log listing
     * @return array
     */
    public function getLogListing()
    {
        $id = $this->getRequest()->getParam('id');
        $data = $this->activityRepository->getActivityLog($id);
        return $data->getData();
    }

    /**
     * Get admin activity details
     * @return array
     */
    public function getAdminDetails()
    {
        $id = $this->getRequest()->getParam('id');
        $activity = $this->activityRepository->getActivityById($id);

        $this->browser->reset();
        $this->browser->setUserAgent($activity->getUserAgent());
        $browser = $this->browser->__toString();

        $logData = [];
        $logData['username'] = $activity->getUsername();
        $logData['module'] = $activity->getModule();
        $logData['name'] = $activity->getName();
        $logData['fullaction'] = $activity->getFullaction();
        $logData['browser'] = $browser;
        $logData['date'] = $activity->getUpdatedAt();
        return $logData;
    }
}
