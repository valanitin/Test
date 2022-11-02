<?php

namespace Meetanshi\SavedCards\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\Session\SessionManagerInterface;
use Meetanshi\SavedCards\Helper\Data;

/**
 * Class SavedCardsConfigProvider
 * @package Meetanshi\SavedCards\Model
 */
class SavedCardsConfigProvider implements ConfigProviderInterface
{
    /**
     * @var Data
     */
    protected $helper;
    /**
     * @var CheckoutSession
     */
    protected $checkoutSession;
    /**
     * @var SessionManagerInterface
     */
    protected $coreSession;

    /**
     * SavedCardsConfigProvider constructor.
     * @param Data $helper
     * @param CheckoutSession $checkoutSession
     * @param SessionManagerInterface $coreSession
     */
    public function __construct(Data $helper, CheckoutSession $checkoutSession, SessionManagerInterface $coreSession)
    {
        $this->helper = $helper;
        $this->checkoutSession = $checkoutSession;
        $this->coreSession = $coreSession;
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        $config = [];
        $showLogo = $this->helper->showLogo();
        $imageUrl = $this->helper->getPaymentLogo();
        $instructions = $this->helper->getInstructions();
        $config['savedcards_imageurl'] = ($showLogo) ? $imageUrl : '';
        $config['savedcards_instructions'] = ($instructions) ? $instructions : '';

        return $config;
    }
}
