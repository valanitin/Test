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
 * @copyright   Copyright (c) 2018 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Amp\Block\Page\Html\Header\Menu;

class Right extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Checkout\Model\SessionFactory
     */
    protected $checkoutSessionFactory;

    /**
     * @var \Magento\Quote\Model\QuoteRepository
     */
    protected $quoteRepository;

    /**
     * @param \Magento\Framework\View\Element\Context   $context
     * @param \Magento\Checkout\Model\SessionFactory    $checkoutSessionFactory
     * @param \Magento\Quote\Model\QuoteRepository      $quoteRepository
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Checkout\Model\SessionFactory $checkoutSessionFactory,
        \Magento\Quote\Model\QuoteRepository $quoteRepository,
        array $data = []
    ) {
        $this->checkoutSessionFactory = $checkoutSessionFactory;
        $this->quoteRepository = $quoteRepository;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve cart items count
     *
     * @return int
     */
    public function getCartSummaryCount()
    {
        if ($quoteId = $this->checkoutSessionFactory->create()->getQuoteId()) {
            return (int)$this->quoteRepository->get($quoteId)->getItemsSummaryQty();
        }

        return null;
    }
}
