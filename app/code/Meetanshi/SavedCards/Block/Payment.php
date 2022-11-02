<?php

namespace Meetanshi\SavedCards\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Payment\Gateway\ConfigInterface;

/**
 * Class Payment
 * @package Meetanshi\SavedCards\Block
 */
class Payment extends Template
{
    /**
     *
     */
    const PAYMENT_CODE = 'savedcards';

    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * Payment constructor.
     * @param Context $context
     * @param ConfigInterface $config
     * @param array $data
     */
    public function __construct(
        Context $context,
        ConfigInterface $config,
        array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->config = $config;
    }

    /**
     * @return false|string
     */
    public function getPaymentConfig()
    {
        return json_encode(
            [
                'code' => self::PAYMENT_CODE,
            ],
            JSON_UNESCAPED_SLASHES
        );
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return self::PAYMENT_CODE;
    }

    /**
     * @return string
     */
    public function toHtml()
    {
        return parent::toHtml();
    }
}
