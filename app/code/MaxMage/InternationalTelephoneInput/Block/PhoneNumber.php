<?php
/**
 *
 * @category   MaxMage
 * @package    mc-magento2
 * @author     MaxMage Core Team <maxmagedev@gmail.com>
 * @date       1/14/2018
 * @copyright  Copyright © 2018 MaxMage. All rights reserved.
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @file       PhoneNumber.php
 */

namespace MaxMage\InternationalTelephoneInput\Block;

use \Magento\Framework\View\Element\Template;
use \Magento\Framework\View\Element\Template\Context;
use \Magento\Framework\Serialize\Serializer\Json;
use \MaxMage\InternationalTelephoneInput\Helper\Data;
use \Magento\Directory\Api\CountryInformationAcquirerInterface;

class PhoneNumber extends Template
{

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var Json
     */
    protected $jsonHelper;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var CountryInformationAcquirerInterface
     */
    protected $countryInformation;

    /**
     * PhoneNumber constructor.
     * @param Context $context
     * @param Json $jsonHelper
     */
    public function __construct(
        Context $context,
        Json $jsonHelper,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        CountryInformationAcquirerInterface $countryInformation,
        Data $helper
    )
    {
        $this->jsonHelper = $jsonHelper;
        $this->helper = $helper;
        $this->_storeManager = $storeManager;
        $this->countryInformation = $countryInformation;
        parent::__construct($context);
    }

    /**
     * @return bool|string
     */
    public function phoneConfig()
    {

        $country_code = $this->getStoreCode();
        $country_code = strtok($country_code, '-');
        $config  = [
            "nationalMode" => false,
            "utilsScript"  => $this->getViewFileUrl('MaxMage_InternationalTelephoneInput::js/utils.js'),
            "preferredCountries" => [$this->helper->preferedCountry()],
            "initialCountry" => $country_code
        ];

        if ($this->helper->allowedCountries()) {
            $config["onlyCountries"] = explode(",", $this->helper->allowedCountries());
        }

        return $this->jsonHelper->serialize($config);
    }
    public function getStoreCode()
    {
        return $this->_storeManager->getStore()->getCode();
    }
}
