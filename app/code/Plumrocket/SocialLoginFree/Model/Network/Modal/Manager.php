<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2022 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\SocialLoginFree\Model\Network\Modal;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Data\Form\FormKey;
use Magento\Framework\Escaper;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Stdlib\Cookie\PublicCookieMetadataFactory;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Layout;
use Plumrocket\SocialLoginFree\Helper\Config;
use Plumrocket\SocialLoginFree\Model\Network\Debug\NetworkLoggerInterface;

/**
 * @since 3.2.0
 */
class Manager
{

    public const SHOW_SHARE_POPUP_COOKIE_NAME = 'pslogin_show_popup';

    /**
     * @var \Magento\Framework\Controller\ResultFactory
     */
    private $resultFactory;

    /**
     * @var \Magento\Framework\View\Layout
     */
    private $layout;

    /**
     * @var \Magento\Framework\Data\Form\FormKey
     */
    private $formKey;

    /**
     * @var \Magento\Framework\Escaper
     */
    private $escaper;

    /**
     * @var \Magento\Framework\Stdlib\CookieManagerInterface
     */
    private $cookieManager;

    /**
     * @var \Magento\Framework\Stdlib\Cookie\PublicCookieMetadataFactory
     */
    private $publicCookieMetadataFactory;

    /**
     * @var \Plumrocket\SocialLoginFree\Model\Network\Debug\NetworkLoggerInterface
     */
    private $debugLog;

    /**
     * @var \Plumrocket\SocialLoginFree\Helper\Config
     */
    private $config;

    /**
     * @param \Magento\Framework\Controller\ResultFactory                            $resultFactory
     * @param \Magento\Framework\View\Layout                                         $layout
     * @param \Magento\Framework\Data\Form\FormKey                                   $formKey
     * @param \Magento\Framework\Escaper                                             $escaper
     * @param \Magento\Framework\Stdlib\CookieManagerInterface                       $cookieManager
     * @param \Magento\Framework\Stdlib\Cookie\PublicCookieMetadataFactory           $publicCookieMetadataFactory
     * @param \Plumrocket\SocialLoginFree\Model\Network\Debug\NetworkLoggerInterface $debugLog
     * @param \Plumrocket\SocialLoginFree\Helper\Config                              $config
     */
    public function __construct(
        ResultFactory $resultFactory,
        Layout $layout,
        FormKey $formKey,
        Escaper $escaper,
        CookieManagerInterface $cookieManager,
        PublicCookieMetadataFactory $publicCookieMetadataFactory,
        NetworkLoggerInterface $debugLog,
        Config $config
    ) {
        $this->resultFactory = $resultFactory;
        $this->layout = $layout;
        $this->formKey = $formKey;
        $this->escaper = $escaper;
        $this->cookieManager = $cookieManager;
        $this->publicCookieMetadataFactory = $publicCookieMetadataFactory;
        $this->debugLog = $debugLog;
        $this->config = $config;
    }

    /**
     * Close OAuth modal.
     *
     * @param bool $isAjax
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function close(bool $isAjax = false): ResultInterface
    {
        if ($isAjax) {
            /** @var \Magento\Framework\Controller\Result\Json $result */
            $result = $this->resultFactory->create(ResultFactory::TYPE_JSON);
            $result->setData(['windowClose' => true]);
        } else {
            /** @var \Magento\Framework\Controller\Result\Raw $result */
            $result = $this->resultFactory->create(ResultFactory::TYPE_RAW);
            $result->setContents($this->wrapJs('window.close();'));
        }

        return $result;
    }

    /**
     * Show collected errors.
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function showCollectedErrors(): ResultInterface
    {
        if ($this->config->isDebugMode()) {
            return $this->showDeveloperErrors();
        }
        return $this->showProductionError();
    }

    /**
     * Show errors.
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function showDeveloperErrors(): ResultInterface
    {
        $this->debugLog->recordLogs();

        /** @var \Magento\Framework\Controller\Result\Raw $result */
        $result = $this->resultFactory->create(ResultFactory::TYPE_RAW);

        try {
            /** @var \Magento\Framework\View\Element\Template $errorBlock */
            $errorBlock = $this->layout->getBlockSingleton(Template::class);
            $errorBlock->setTemplate('Plumrocket_SocialLoginFree::error.phtml');
            $errorBlock->setError($this->debugLog->getDebugInfo());
            $result->setContents($errorBlock->toHtml());
        } catch (LocalizedException $e) {
            $result->setContents($e->getMessage());
        }

        return $result;
    }

    /**
     * Show error in production mode.
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function showProductionError(): ResultInterface
    {
        /** @var \Magento\Framework\Controller\Result\Raw $result */
        $result = $this->resultFactory->create(ResultFactory::TYPE_RAW);
        $result->setContents(
            __(
                'The Login Application was not configured correctly. If your are the admin of store: ' .
                'Please activate “Enable Logging” in Magento Login Extension and try again to see err' .
                'or details.'
            )
        );

        return $result;
    }

    /**
     * Get page with js.
     *
     * @param string $js
     * @return string
     */
    private function wrapJs(string $js): string
    {
        return "<html><head></head><body><script>{$js}</script></body></html>";
    }

    /**
     * Redirect to given url.
     *
     * @param bool   $isAjax
     * @param string $redirectUrl
     * @param string $authAction
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function redirect(bool $isAjax, string $redirectUrl, string $authAction): ResultInterface
    {
        if ($isAjax) {
            /** @var \Magento\Framework\Controller\Result\Json $result */
            $result = $this->resultFactory->create(ResultFactory::TYPE_JSON);
            return $result->setData(['redirectUrl' => $redirectUrl]);
        }

        /** @var \Magento\Framework\Controller\Result\Raw $result */
        $result = $this->resultFactory->create(ResultFactory::TYPE_RAW);
        $result->setContents($this->wrapJs('window.close();'));

        try {
            $formKey = $this->formKey->getFormKey();
        } catch (LocalizedException $e) {
            $formKey = '';
        }

        $encodedUrl = $this->escaper->escapeJs(base64_encode($redirectUrl));
        $jsAction = <<<JSACTION
var pslDocument = window.opener ? window.opener.document : document;

if (null == pslDocument.getElementById("pslogin-login-referer")) {
    console.error('pslDocument.getElementById("pslogin-login-referer") not found');
    window.location.href = "$redirectUrl";
} else {
    pslDocument.getElementById("pslogin-login-referer").value = "$encodedUrl";
    pslDocument.getElementById("pslogin-login-action").value = "$authAction";
    pslDocument.getElementsByName('form_key')[0].value = '{$formKey}';
    pslDocument.getElementById("pslogin-login-submit").click();
}
JSACTION;

        $result->setContents(
            $this->wrapJs(
                "if(window.opener && window.opener.location && !window.opener.closed) {window.close();}; {$jsAction};"
            )
        );

        return $result;
    }

    /**
     * Check if we need to show share popup.
     *
     * @return bool
     */
    public function showSharePopup(): bool
    {
        /** @var \Magento\Framework\Stdlib\Cookie\PublicCookieMetadata $publicCookieMetadata */
        $publicCookieMetadata = $this->publicCookieMetadataFactory->create(['metadata' => []]);
        $publicCookieMetadata
            ->setDuration(600)
            ->setPath('/');

        try {
            $this->cookieManager->setPublicCookie(self::SHOW_SHARE_POPUP_COOKIE_NAME, 1, $publicCookieMetadata);
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }
}
