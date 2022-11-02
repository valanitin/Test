<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Meta
 */


namespace Amasty\Meta\Controller\Adminhtml;

use Amasty\Meta\Api\ConfigRepositoryInterface;
use Amasty\Meta\Api\Data\ConfigInterfaceFactory;
use Psr\Log\LoggerInterface;

abstract class Config extends \Magento\Backend\App\Action
{
    protected $_title = 'Meta Tags Template (Categories)';
    protected $_blockName = 'Config';
    protected $_isCustom = false;

    /**
     * @var \Magento\Backend\Model\View\Result\ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @var ConfigInterfaceFactory
     */
    protected $configFactory;

    /**
     * @var ConfigRepositoryInterface
     */
    protected $configRepository;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry,
        ConfigInterfaceFactory $configFactory,
        ConfigRepositoryInterface $configRepository,
        LoggerInterface $logger
    ) {
        parent::__construct($context);
        $this->messageManager = $context->getMessageManager();
        $this->resultForwardFactory = $resultForwardFactory;
        $this->resultPageFactory = $resultPageFactory;
        $this->registry = $registry;
        $this->configFactory = $configFactory;
        $this->configRepository = $configRepository;
        $this->logger = $logger;
    }

    /**
     * Initiate action
     *
     * @return this
     */
    protected function _initAction()
    {
        $this->_view->loadLayout();
        $this->_setActiveMenu('Amasty_Meta::config')->_addBreadcrumb(
            __('Meta Tags Template (Categories)'),
            __('Meta Tags Template (Categories)')
        );

        return $this;
    }

    /**
     * Determine if authorized to perform group actions.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Amasty_Meta::config');
    }
}
