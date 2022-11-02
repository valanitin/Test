<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */


namespace Amasty\Shopby\Block\Adminhtml\Form\Renderer\Fieldset;

use Magento\Framework\Data\Form\Element\Factory;
use Magento\Store\Model\Store;

class MultiStore extends \Magento\Backend\Block\Widget\Form\Renderer\Fieldset\Element
{
    /**
     * @var string
     */
    protected $_template = 'form/renderer/fieldset/multistore.phtml';

    /**
     * @var Factory
     */
    protected $elementFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Amasty\Shopby\Helper\Group
     */
    private $groupHelper;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Data\Form\Element\Factory $elementFactory,
        \Magento\Framework\Registry $registry,
        \Amasty\Shopby\Helper\Group $groupHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->elementFactory = $elementFactory;
        $this->registry = $registry;
        $this->groupHelper = $groupHelper;
    }

    /**
     * @param $storeId
     * @return null|string
     */
    public function getStoreValue($storeId)
    {
        if ($value = $this->getElement()->getValue()) {
            $value = $this->groupHelper->chooseGroupLabel($value, $storeId);
        }

        return $value;
    }

    /**
     * @return \Magento\Store\Api\Data\StoreInterface[]
     */
    public function getStores()
    {
        return $this->_storeManager->getStores();
    }

    /**
     * @param \Magento\Store\Api\Data\StoreInterface $store
     * @return bool
     */
    public function isDefaultStore(\Magento\Store\Api\Data\StoreInterface $store)
    {
        return $store->getStoreId() == Store::DEFAULT_STORE_ID;
    }
}
