<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2021 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license/ End-user License Agreement
 */

namespace Plumrocket\SocialLoginFree\Block\Adminhtml\System\Config\Form;

use Magento\Backend\App\Area\FrontNameResolver;
use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Exception\NoSuchEntityException;
use Plumrocket\SocialLoginFree\Model\SystemConfig\GetCurrentStoreCode;

/**
 * @since 3.1.0
 */
class DeletionCallbackUrl extends Field
{
    /**
     * @var \Plumrocket\SocialLoginFree\Model\SystemConfig\GetCurrentStoreCode
     */
    private $getCurrentStoreCode;
    /**
     * @var \Magento\Backend\App\Area\FrontNameResolver
     */
    private $frontNameResolver;

    /**
     * @param \Magento\Backend\Block\Template\Context                            $context
     * @param \Plumrocket\SocialLoginFree\Model\SystemConfig\GetCurrentStoreCode $getCurrentStoreCode
     * @param \Magento\Backend\App\Area\FrontNameResolver                        $frontNameResolver
     * @param array                                                              $data
     */
    public function __construct(
        Context $context,
        GetCurrentStoreCode $getCurrentStoreCode,
        FrontNameResolver $frontNameResolver,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->getCurrentStoreCode = $getCurrentStoreCode;
        $this->frontNameResolver = $frontNameResolver;
    }

    /**
     * @param AbstractElement $element
     * @return string
     * @throws NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _getElementHtml(AbstractElement $element): string
    {
        $storeCode = $this->getCurrentStoreCode->execute();

        $url = $this->_storeManager->getStore($storeCode)->getUrl('pslogin/facebookdeletion/request');

        $url = str_replace(
            DIRECTORY_SEPARATOR . $this->frontNameResolver->getFrontName() . DIRECTORY_SEPARATOR,
            DIRECTORY_SEPARATOR,
            $url
        );

        if (false !== ($length = strpos($url, '?'))) {
            $url = substr($url, 0, $length);
        }

        $url = preg_replace('/key\/(.*)/', '', $url);

        if (preg_match('/[a-z]+\.php\//', $url) > 0) {
            $url = str_replace('index.php/', '', $url);
        }

        return '<input id="' . $element->getHtmlId() . '" ' .
            'type="text" ' .
            'name="" ' .
            'value="' . $url . '" ' .
            'class="input-text psloginfree-callbackurl-autofocus" ' .
            'style="background-color: #EEE; color: #999;" ' .
            'readonly="readonly" />';
    }
}
