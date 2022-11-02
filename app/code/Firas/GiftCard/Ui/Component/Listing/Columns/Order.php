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

class Order extends Column
{
    /**
     * @var UrlInterface
     */
    protected $urlBuilder;
    
    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $_order;

    /**
     * Constructor.
     *
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param \Magento\Sales\Model\OrderFactory $order
     * @param UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        \Magento\Sales\Model\OrderFactory $order,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        $this->_order = $order;
        $this->urlBuilder = $urlBuilder;
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
                if (isset($item['order_id']) && ($item['order_id'] !="" || $item['order_id'] !=null)) {
                    $incrementId = $this->_order->create()->load($item['order_id'])->getIncrementId();
                    $url = $this->urlBuilder->getUrl(
                        'sales/order/view',[
                            'order_id' => $item['order_id']
                        ]
                    );
                    $item['order_id'] = '<a href="'.$url.'">'.$incrementId.'</a>';
                }
            }
        }
        return $dataSource;
    }
}
