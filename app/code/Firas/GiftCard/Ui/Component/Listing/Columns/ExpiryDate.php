<?php
/**
 * @Author      Firas Developers
 * @package     Firas_GiftCard
 * @copyright   Copyright (c) 2019 MAGETOP (https://www.firas.com)
 * @terms       https://www.firas.com/terms
 * @license     https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 **/
namespace Firas\GiftCard\Ui\Component\Listing\Columns;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

class ExpiryDate extends Column
{
    /**
     * @var \Firas\GiftCard\Helper\Data
     */
    protected $_helper;

    /**
     * Constructor.
     *
     * @param ContextInterface             $context
     * @param UiComponentFactory           $uiComponentFactory
     * @param array                        $components
     * @param array                        $data
     * @param \Firas\GiftCard\Helper\Data $helperData
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = [],
        \Firas\GiftCard\Helper\Data $helperData
    ) {
        $this->_helper = $helperData;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source.
     *
     * @param array $dataSource
     *
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $item['expiry'] = $this->_helper->createExpirationDateOfGiftCard($item['duration'], $item['alloted']);
            }
        }
        return $dataSource;
    }
}
