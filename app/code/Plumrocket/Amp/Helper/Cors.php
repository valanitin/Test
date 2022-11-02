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
 * @package     Plumrocket_Amp
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Amp\Helper;

class Cors extends \Magento\Framework\App\Helper\AbstractHelper
{
    const DEFAULT_ACCESS_CONTROL_ORIGIN = 'cdn.ampproject.org';

    /**
     * @var array
     */
    private $allowedOrigins = [];

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Magento\Framework\App\Response\Http
     */
    private $response;

    /**
     * @var \Magento\Framework\Serialize\SerializerInterface
     */
    private $serializer;

    /**
     * Cors constructor.
     *
     * @param \Magento\Framework\App\Helper\Context            $context
     * @param \Magento\Store\Model\StoreManagerInterface       $storeManager
     * @param \Magento\Framework\App\Response\Http             $response
     * @param \Magento\Framework\Serialize\SerializerInterface $serializer
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Response\Http $response,
        \Magento\Framework\Serialize\SerializerInterface $serializer
    ) {
        parent::__construct($context);
        $this->storeManager = $storeManager;
        $this->response = $response;
        $this->serializer = $serializer;
    }

    /**
     * Processing HTTP-Headers for cross domain requests
     * Setting additional headers for same-origin and cross-origin requests
     * according to https://github.com/ampproject/amphtml/blob/master/spec/amp-cors-requests.md
     *
     * @param \Magento\Framework\Controller\AbstractResult|null $result
     * @return $this
     */
    public function prepareHeadersForAmpResponse(\Magento\Framework\Controller\AbstractResult $result = null)
    {
        return $this->removeSameOriginHeaders()->setAccessControlHeaders($result);
    }

    /**
     * Set headers for amp form redirect
     * Url must be https://
     *
     * @param \Magento\Framework\Controller\AbstractResult $result
     * @param string                                       $url
     * @return $this
     */
    public function setFormRedirectHeaders(\Magento\Framework\Controller\AbstractResult $result, $url)
    {
        $result
            ->setHeader(
                'AMP-Redirect-To',
                $url
            )
            ->setHeader(
                'Access-Control-Expose-Headers',
                'AMP-Redirect-To, AMP-Access-Control-Allow-Source-Origin',
                true
            );
        return $this;
    }

    /**
     * Retrieve source origin for current  page publisher
     * @return string
     */
    public function getAccessControlOrigin() : string
    {
        if ($httpOrigin = $this->_getRequest()->getServer('HTTP_ORIGIN')) {
            return $httpOrigin;
        }

        /**
         * Alternative way to detecting
         * Detecting source origin by magento base url
         */
        if ($baseUrl = $this->storeManager->getStore()->getBaseUrl()) {
            $urlData = parse_url($baseUrl);
            if (! empty($urlData['host'])) {
                return (
                    'https://' . str_replace(['-', '.'], ['--', '-'], $urlData['host'])
                    . '.' . self::DEFAULT_ACCESS_CONTROL_ORIGIN
                );
            }
        }

        /**
         * Return source origin by default
         */
        return 'https://' . self::DEFAULT_ACCESS_CONTROL_ORIGIN;
    }

    /**
     * Verify and send error on fail
     */
    public function stopNotVerifiedRequest()
    {
        if (! $this->verifyRequest()) {
            $responseBody = $this->serializer->serialize(['error' => __('Verify failed')]);

            $this->getResponse()
                 ->setHttpResponseCode(400)
                 ->setBody($responseBody)
                 ->sendHeadersAndExit();
        }
    }

    /**
     * @return bool
     */
    public function verifyRequest() : bool
    {
        $request = $this->_getRequest();
        $verified = false;

        if ($request->getHeader('AMP-Same-Origin') === 'true') {
            $verified = true;
        } elseif ($currentOrigin = $request->getHeader('Origin')) {
            $sourceOrigin = $request->getParam('__amp_source_origin');

            /**
             * "__amp_source_origin" in Google AMP Cache may have different protocol from the real and
             * as module cannot work on http, we change http to https.
             * If we don't do that "Add to Cart" may not work
             *
             * @link https://developers.google.com/amp/cache/
             */
            $sourceOrigin = str_replace('http://', 'https://', $sourceOrigin);

            $host = 'https://' . parse_url($this->storeManager->getStore()->getBaseUrl(), PHP_URL_HOST);
            $allowedOrigins = $this->getAllowedOrigins();

            if (array_key_exists($currentOrigin, $allowedOrigins)
                && $sourceOrigin === $host
            ) {
                $verified = true;
            }
        }

        return $verified;
    }

    /**
     * @return bool[]
     */
    private function getAllowedOrigins()
    {
        if (empty($this->allowedOrigins)) {
            $host = 'https://' . parse_url($this->storeManager->getStore()->getBaseUrl(), PHP_URL_HOST);
            $this->allowedOrigins = [
                $host => 1,
                str_replace('.', '-', $host) . '.cdn.ampproject.org' => 1,
                $host . '.amp.cloudflare.com' => 1,
                'cdn.ampproject.org' => 1,
            ];
        }

        return $this->allowedOrigins;
    }

    /**
     * @return $this
     */
    private function removeSameOriginHeaders()
    {
        $headers = $this->response->getHeaders();
        $this->response->clearHeaders();

        foreach ($headers as $header) {
            if (($header['name'] !== 'X-Frame-Options')
                && ($header['name'] !== 'Content-Security-Policy')
            ) {
                $this->response->setHeader($header['name'], $header['value'], $header['replace']);
            }
        }

        return $this;
    }

    /**
     * Set Access Control Headers
     *
     * @param \Magento\Framework\Controller\AbstractResult|null $result
     * @return $this
     */
    private function setAccessControlHeaders(\Magento\Framework\Controller\AbstractResult $result = null)
    {
        $sourceOrigin = $this->_request->getParam('__amp_source_origin');

        if (! $sourceOrigin) {
            $urlData = parse_url($this->storeManager->getStore()->getBaseUrl());

            if (! empty($urlData['scheme']) && !empty($urlData['host'])) {
                $port = ! empty($urlData['port']) ? (':' . $urlData['port']) : '';
                $sourceOrigin = $urlData['scheme'] . '://' . $urlData['host'] . $port;
            }
        }

        /** @var \Magento\Framework\Controller\AbstractResult|\Magento\Framework\App\Response\Http $objectForHeader */
        $objectForHeader = $result ?: $this->response;

        $objectForHeader
            ->setHeader(
                'Access-Control-Allow-Origin',
                $this->getAccessControlOrigin(),
                true
            )
            ->setHeader(
                'AMP-Access-Control-Allow-Source-Origin',
                $sourceOrigin,
                true
            )
            ->setHeader(
                'Access-Control-Expose-Headers',
                'AMP-Access-Control-Allow-Source-Origin',
                true
            )
            ->setHeader(
                'Access-Control-Allow-Methods',
                'POST, GET, OPTIONS',
                true
            )
            ->setHeader(
                'Access-Control-Allow-Headers',
                'Content-Type, Content-Length, Accept-Encoding, X-CSRF-Token',
                true
            )
            ->setHeader(
                'Access-Control-Allow-Credentials',
                'true',
                true
            );

        return $this;
    }
}
