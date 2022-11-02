<?php

declare(strict_types=1);

namespace LuxuryUnlimited\Testimonial\Plugin\Controller;

use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magiccart\Testimonial\Controller\Index\Post;

/**
 * plugin TestimonialPostPlugin
 */
class TestimonialPostPlugin
{
    /**
     * @var Context
     */
    protected $context;

    /**
     * @var ResponseInterface
     */
    protected  $response;

    /**
     * @var UrlInterface
     */
    protected $url;

    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param Context $context
     * @param UrlInterface $url
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Context $context,
        UrlInterface $url,
        StoreManagerInterface $storeManager
    ) {
        $this->context = $context;
        $this->response = $context->getResponse();
        $this->url = $url;
        $this->_storeManager = $storeManager;
    }

    /**
     * @param Post $subject
     * @return Post|void
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function beforeExecute(
        Post $subject
    ) {
        if($this->getStoreCode() == 'in-en')
        {
            return $subject;
        }
        return $this->response->setRedirect($this->url->getUrl('noroute'));
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getStoreCode(): string
    {
        return $this->_storeManager->getStore()->getCode();
    }
}