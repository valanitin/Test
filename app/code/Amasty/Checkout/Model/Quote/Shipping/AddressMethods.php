<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Checkout
 */


namespace Amasty\Checkout\Model\Quote\Shipping;

use Amasty\Checkout\Model\Config;
use Amasty\Checkout\Model\Utils\DataObjectDataBackup;
use Magento\Quote\Api\Data\AddressInterface;
use Magento\Quote\Api\Data\CartExtensionFactory;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Api\Data\ShippingMethodInterface;
use Magento\Quote\Api\ShippingMethodManagementInterface;
use Magento\Quote\Model\Quote\Address;
use Magento\Quote\Model\ShippingAssignment;
use Magento\Quote\Model\ShippingAssignmentFactory;
use Magento\Quote\Model\ShippingFactory;

/**
 * Process shipping methods for shipping address.
 * Assign shipping method to address.
 *
 * @since 3.0.0
 */
class AddressMethods
{
    private $storage = [];

    /**
     * @var ShippingMethodManagementInterface
     */
    private $methodManagement;

    /**
     * @var DataObjectDataBackup
     */
    private $dataObjectDataBackup;

    /**
     * @var Config
     */
    private $checkoutConfig;

    /**
     * @var CartExtensionFactory
     */
    private $cartExtensionFactory;

    /**
     * @var ShippingAssignmentFactory
     */
    private $shippingAssignmentFactory;

    /**
     * @var ShippingFactory
     */
    private $shippingFactory;

    public function __construct(
        ShippingMethodManagementInterface $methodManagement,
        DataObjectDataBackup $dataObjectDataBackup,
        Config $checkoutConfig,
        CartExtensionFactory $cartExtensionFactory,
        ShippingAssignmentFactory $shippingAssignmentFactory,
        ShippingFactory $shippingFactory
    ) {
        $this->methodManagement = $methodManagement;
        $this->dataObjectDataBackup = $dataObjectDataBackup;
        $this->checkoutConfig = $checkoutConfig;
        $this->cartExtensionFactory = $cartExtensionFactory;
        $this->shippingAssignmentFactory = $shippingAssignmentFactory;
        $this->shippingFactory = $shippingFactory;
    }

    /**
     * Assign shipping method to shipping address
     *
     * @param CartInterface $quote
     * @param AddressInterface $shippingAddress
     *
     * @return CartInterface
     */
    public function processShippingAssignment(CartInterface $quote, AddressInterface $shippingAddress)
    {
        $shippingMethod = $this->getSelectedShippingMethod($shippingAddress);

        $cartExtension = $quote->getExtensionAttributes();
        if ($cartExtension === null) {
            $cartExtension = $this->cartExtensionFactory->create();
        }

        $shippingAssignments = $cartExtension->getShippingAssignments();
        if (empty($shippingAssignments)) {
            /** @var ShippingAssignment $shippingAssignment */
            $shippingAssignment = $this->shippingAssignmentFactory->create();
        } else {
            $shippingAssignment = $shippingAssignments[0];
        }

        if (!$shippingMethod) {
            $cartExtension->setShippingAssignments([]);
            $shippingAddress->setShippingMethod(null);

            return $quote->setExtensionAttributes($cartExtension);
        }

        $shipping = $shippingAssignment->getShipping();
        if ($shipping === null) {
            $shipping = $this->shippingFactory->create();
        }

        $carrierCode = $shippingMethod->getCarrierCode();
        $shippingAddress->setLimitCarrier($carrierCode);
        $methodCode = $shippingMethod->getMethodCode();
        $method = $carrierCode . '_' . $methodCode;
        $shippingAddress->setShippingMethod($method);
        $shipping->setAddress($shippingAddress);
        $shipping->setMethod($method);
        $shippingAssignment->setShipping($shipping);
        $cartExtension->setShippingAssignments([$shippingAssignment]);
        $quote->setTotalsCollectedFlag(false);

        return $quote->setExtensionAttributes($cartExtension);
    }

    /**
     * Get selected shipping method object.
     * Algorithm is the same as at front checkout-data-resolver.resolveShippingRates
     *
     * @param AddressInterface $shippingAddress
     *
     * @return ShippingMethodInterface|null
     */
    public function getSelectedShippingMethod(AddressInterface $shippingAddress)
    {
        $activeMethods = $this->getActiveShippingMethods($shippingAddress);

        if (count($activeMethods) === 1) {
            return reset($activeMethods);
        }

        if ($selectedMethod = $shippingAddress->getShippingMethod()) {
            foreach ($activeMethods as $method) {
                if ($method->getCarrierCode() . '_' . $method->getMethodCode() == $selectedMethod) {
                    return $method;
                }
            }
        }

        if ($defaultMethod = $this->checkoutConfig->getDefaultShippingMethod()) {
            foreach ($activeMethods as $method) {
                if ($method->getCarrierCode() . '_' . $method->getMethodCode() == $defaultMethod) {
                    return $method;
                }
            }
        }

        return null;
    }

    /**
     * @param AddressInterface $shippingAddress
     *
     * @return ShippingMethodInterface[]
     */
    private function getActiveShippingMethods(AddressInterface $shippingAddress)
    {
        $methods = $this->getShippingMethods($shippingAddress);
        foreach ($methods as $key => $method) {
            if (!$method->getAvailable()) {
                unset($methods[$key]);
            }
        }

        return $methods;
    }

    /**
     * @param Address|AddressInterface $shippingAddress
     *
     * @return ShippingMethodInterface[] An array of shipping methods.
     */
    public function getShippingMethods(AddressInterface $shippingAddress)
    {
        $addressId = $shippingAddress->getId();
        if (isset($this->storage[$addressId])) {
            return $this->storage[$addressId];
        }
        if ($shippingAddress->getCustomerAddressId()) {
            // fix for 2.3.2 and earlier
            // when we estimateByAddressId - in $shippingAddress we have array in region field instead of string
            $shippingData = $this->dataObjectDataBackup->backupData(
                $shippingAddress,
                ['region', 'region_code', 'region_id']
            );
            $methods = $this->methodManagement->estimateByAddressId(
                $shippingAddress->getQuoteId(),
                $shippingAddress->getCustomerAddressId()
            );
            $this->dataObjectDataBackup->restoreData($shippingAddress, $shippingData);
        } else {
            $methods = $this->methodManagement->estimateByExtendedAddress(
                $shippingAddress->getQuoteId(),
                $shippingAddress
            );
        }

        $this->storage[$addressId] = $methods;

        return $this->storage[$addressId];
    }
}
