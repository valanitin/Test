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
 * @package     Plumrocket Amp v2.x.x
 * @copyright   Copyright (c) 2018 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Amp\Controller\Api;

class Recently extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Plumrocket\Amp\Block\Catalog\Product\Widget\RecentlyViewed
     */
    private $recentlyViewed;

    /**
     * @var \Plumrocket\Amp\Model\Result\AmpJsonFactory
     */
    private $ampResultFactory;

    /**
     * Recently constructor.
     *
     * @param \Magento\Framework\App\Action\Context                       $context
     * @param \Plumrocket\Amp\Block\Catalog\Product\Widget\RecentlyViewed $recentlyViewed
     * @param \Plumrocket\Amp\Model\Result\AmpJsonFactory                 $ampResultFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Plumrocket\Amp\Block\Catalog\Product\Widget\RecentlyViewed $recentlyViewed,
        \Plumrocket\Amp\Model\Result\AmpJsonFactory $ampResultFactory
    ) {
        $this->recentlyViewed = $recentlyViewed;
        parent::__construct($context);
        $this->ampResultFactory = $ampResultFactory;
    }

    /**
     * @return \Plumrocket\Amp\Model\Result\AmpJson
     */
    public function execute()
    {
        $recentlyViewed = $this->recentlyViewed
            ->initPageSize()
            ->getList();

        $ampJsonResponse = $this->ampResultFactory->create();
        if ($recentlyViewed) {
            $ampJsonResponse->setData(['items' => $recentlyViewed]);
        } else {
            $ampJsonResponse->setData([]);
        }

        return $ampJsonResponse;
    }
}
