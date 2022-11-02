<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\SocialLoginFree\Model\Frontend;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Data\Form\FormKey;
use Magento\Framework\Escaper;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Stdlib\Cookie\PublicCookieMetadataFactory;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Layout;
use Plumrocket\SocialLoginFree\Model\Account;

class PopupManager
{
    const SHOW_SHARE_POPUP_COOKIE_NAME = 'pslogin_show_popup';

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
     * PopupManager constructor.
     *
     * @param \Magento\Framework\Controller\ResultFactory                  $resultFactory
     * @param \Magento\Framework\View\Layout                               $layout
     * @param \Magento\Framework\Data\Form\FormKey                         $formKey
     * @param \Magento\Framework\Escaper                                   $escaper
     * @param \Magento\Framework\Stdlib\CookieManagerInterface             $cookieManager
     * @param \Magento\Framework\Stdlib\Cookie\PublicCookieMetadataFactory $publicCookieMetadataFactory
     */
    public function __construct(
        ResultFactory $resultFactory,
        Layout $layout,
        FormKey $formKey,
        Escaper $escaper,
        CookieManagerInterface $cookieManager,
        PublicCookieMetadataFactory $publicCookieMetadataFactory
    ) {
        $this->resultFactory = $resultFactory;
        $this->layout = $layout;
        $this->formKey = $formKey;
        $this->escaper = $escaper;
        $this->cookieManager = $cookieManager;
        $this->publicCookieMetadataFactory = $publicCookieMetadataFactory;
    }

    /**
     * @param bool $isAjax
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function close(bool $isAjax): ResultInterface
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
     * @param \Plumrocket\SocialLoginFree\Model\Account $model
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function showDeveloperErrors(Account $model): ResultInterface
    {
        $model->recordLog();

        /** @var \Magento\Framework\Controller\Result\Raw $result */
        $result = $this->resultFactory->create(ResultFactory::TYPE_RAW);

        try {
            /** @var \Magento\Framework\View\Element\Template $errorBlock */
            $errorBlock = $this->layout->getBlockSingleton(Template::class);
            $errorBlock->setTemplate('Plumrocket_SocialLoginFree::error.phtml');
            $errorBlock->setError($model->getDebugErrors());
            $result->setContents($errorBlock->toHtml());
        } catch (LocalizedException $e) {
            $result->setContents($e->getMessage());
        }

        return $result;
    }

    /**
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
     * @param string $js
     * @return string
     */
    private function wrapJs(string $js): string
    {
        return "<html><head></head><body><script>{$js}</script></body></html>";
    }

    /**
     * @param bool   $isAjax
     * @param string $redirectUrl
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function redirect(bool $isAjax, string $redirectUrl): ResultInterface
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
        $jsAction = "
            var pslDocument = window.opener ? window.opener.document : document;
            pslDocument.getElementById('pslogin-login-referer').value = '{$encodedUrl}';
            pslDocument.getElementsByName('form_key')[0].value = '{$formKey}';
            pslDocument.getElementById('pslogin-login-submit').click();
        ";

        $result->setContents(
            $this->wrapJs(
                "if(window.opener && window.opener.location && !window.opener.closed) {window.close();}; {$jsAction};"
            )
        );

        return $result;
    }

    /**
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
