<?php
/**
 * Plumrocket Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End-user License Agreement
 * that is available through the world-wide-web at this URL:
 * http://wiki.plumrocket.net/wiki/EULA
 * If you are unable to obtain it through the world-wide-web, please
 * send an email to support@plumrocket.com so we can send you a copy immediately.
 *
 * @package     Plumrocket_Amp 2.x.x
 * @copyright   Copyright (c) 2016 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Amp\Block\Page\Html;

class Messages extends \Magento\Framework\View\Element\Template
{
    /**
     * Cookies name for messages
     */
    const MESSAGES_COOKIES_NAME = 'mage-messages';

    /**
     * @var string
     */
    public $template = "html/messages.phtml";

    /**
     * @var \Magento\Framework\Stdlib\CookieManagerInterface
     */
    private $cookieManager;

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    private $jsonHelper;

    /**
     * @var \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory|null
     */
    private $cookieMetadataFactory = null;

    /**
     * @var array
     */
    protected $_messages = null;

    /**
     * Messages constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context       $context
     * @param \Magento\Framework\Stdlib\CookieManagerInterface       $cookieManager
     * @param \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory
     * @param \Magento\Framework\Json\Helper\Data                    $jsonHelper
     * @param array                                                  $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
        \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->cookieManager = $cookieManager;
        $this->jsonHelper = $jsonHelper;
        $this->cookieMetadataFactory = $cookieMetadataFactory;

        $this->_initMessages();
    }

    /**
     * Has messages
     * @return boolean
     */
    public function hasMessages()
    {
        return (is_array($this->_messages) && count($this->_messages)) ? true : false;
    }

    /**
     * Return messages array from cookie and clean cookie self::MESSAGES_COOKIES_NAME
     *
     * @param bool $clear
     * @return array
     */
    public function getMessages($clear = false)
    {
        // Here delete cookie not working with Magento FPC
        // In kernel->process removing header Set-Cookie
        return $this->_messages;
    }

    /**
     * Get messages stored in cookies to messages property
     * @return object
     */
    protected function _initMessages()
    {
        try {
            $messages = $this->jsonHelper->jsonDecode(
                $this->cookieManager->getCookie(self::MESSAGES_COOKIES_NAME, $this->jsonHelper->jsonEncode([]))
            );
            $this->_messages = is_array($messages) ? $messages : [];
        } catch (\Zend_Json_Exception $e) {
            $this->_messages = [];
        }

        return $this;
    }
}
