<?php
/**
 * Revered Technologies Pvt. Ltd.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://store.reveredtech.com/Revered-LICENSE-COMMUNITY.txt
 *
 ********************************************************************
 * @category   Revered
 * @package    Revered_GuestWishlist
 * @copyright  Copyright (c) Revered Technologies Pvt. Ltd. (http://www.reveredtech.com)
 * @license    http://store.reveredtech.com/Revered-LICENSE-COMMUNITY.txt
 */
namespace Revered\GuestWishlist\Model;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\ProductMetadataInterface;

/**
 * Class SerializerBridge
 * @package Revered\GuestWishlist\Model
 */
class SerializerBridge {

    /**
     * @var mixed
     */
    protected $serializer;

    /**
     * @var ProductMetadataInterface
     */
    protected $productMetadata;

    /**
     * SerializerBridge constructor.
     * @param ProductMetadataInterface $productMetadata
     */
    public function __construct(
        ProductMetadataInterface $productMetadata)
    {
        $this->productMetadata =  $productMetadata;
        if (version_compare($this->productMetadata->getVersion(), '2.2.0') != -1) {
            $this->serializer = ObjectManager::getInstance()->get(\Magento\Framework\Serialize\SerializerInterface::class);
        }
    }

    /**
     * @param array|bool|float|int|null|string $data
     * @return bool|string|void
     */
    public function serialize($data) {
        if(isset($this->serializer)) {
            return $this->serializer->serialize($data);
        }

        return \serialize($data);
    }

    /**
     * @param string $string
     * @return array|bool|float|int|null|string|void
     */
    public function unserialize($string) {
        if(isset($this->serializer)) {
            return $this->serializer->unserialize($string);
        }
        return \unserialize($string);
    }

}