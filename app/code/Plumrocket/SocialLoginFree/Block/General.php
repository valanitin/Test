<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2015 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

namespace Plumrocket\SocialLoginFree\Block;

class General extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Plumrocket\SocialLoginFree\Helper\Data
     */
    protected $dataHelper;

    /**
     * @var \Magento\Framework\Serialize\SerializerInterface
     */
    private $serializer;

    /**
     * General constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Plumrocket\SocialLoginFree\Helper\Data          $dataHelper
     * @param \Magento\Framework\Serialize\SerializerInterface $serializer
     * @param array                                            $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Plumrocket\SocialLoginFree\Helper\Data $dataHelper,
        \Magento\Framework\Serialize\SerializerInterface $serializer,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->dataHelper = $dataHelper;
        $this->serializer = $serializer;
    }

    /**
     * @return string|void
     */
    protected function _toHtml()
    {
        if (!$this->dataHelper->moduleEnabled()) {
            return;
        }

        return parent::_toHtml();
    }

    /**
     * @return string
     */
    public function getSkipModules()
    {
        $skipModules = $this->dataHelper->getRefererLinkSkipModules();
        return $this->serializer->serialize($skipModules);
    }
}
