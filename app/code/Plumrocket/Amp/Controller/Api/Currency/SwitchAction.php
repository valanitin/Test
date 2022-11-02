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
 * @copyright   Copyright (c) 2019 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Amp\Controller\Api\Currency;

use Magento\Framework\App\Action\Context as ActionContext;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\RequestInterface;
use Magento\Store\Model\StoreManagerInterface;
use Plumrocket\Amp\Model\Result\AmpJson;
use Plumrocket\Amp\Model\Result\AmpJsonFactory;

class SwitchAction extends \Magento\Framework\App\Action\Action implements
    \Plumrocket\Amp\Model\MagentoTwoTwo\CsrfAwareActionInterface
{
    use \Plumrocket\Amp\Controller\ValidateForCsrfTrait;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var AmpJsonFactory
     */
    private $ampResultFactory;

    /**
     * SwitchAction constructor.
     *
     * @param ActionContext         $context
     * @param StoreManagerInterface $storeManager
     * @param AmpJsonFactory        $ampResultFactory
     */
    public function __construct(
        ActionContext $context,
        StoreManagerInterface $storeManager,
        AmpJsonFactory $ampResultFactory
    ) {
        parent::__construct($context);
        $this->storeManager = $storeManager;
        $this->ampResultFactory = $ampResultFactory;
    }

    /**
     * @return AmpJson
     */
    public function execute() : AmpJson
    {
        $ampJsonResult = $this->ampResultFactory->create();

        $currency = (string)$this->getRequest()->getParam('currency');

        if ($currency) {
            $this->storeManager->getStore()->setCurrentCurrencyCode($currency);
        }

        $backUrl = (string)$this->getRequest()->getParam('back_url');
        if (! $backUrl) {
            $backUrl = $this->storeManager->getStore()->getBaseUrl() . '?amp=1';
        }

        $ampJsonResult->setFormRedirect($backUrl);

        return $ampJsonResult;
    }
}
