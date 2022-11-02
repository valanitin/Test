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

class IsExpire extends Column
{
    /**
     * @var \Firas\GiftCard\Helper\Data
     */
    protected $_helper;
    
    /**
     * @var \Firas\GiftCard\Model\GiftUserFactory
     */
    protected $_giftuser;

    /**
     * Constructor.
     *
     * @param ContextInterface                         $context
     * @param UiComponentFactory                       $uiComponentFactory
     * @param \Firas\GiftCard\Helper\Data             $helperData
     * @param \Firas\GiftCard\Model\GiftUserFactory   $giftUser
     * @param array                                    $components
     * @param array                                    $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        \Firas\GiftCard\Helper\Data $helperData,
        \Firas\GiftCard\Model\GiftUserFactory $giftUser,
        array $components = [],
        array $data = []
    ) {
        $this->_helperData = $helperData;
        $this->_giftuser = $giftUser;
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
                $isExpire = $this->_helperData->checkExpirationOfGiftCard($item['alloted'], $item['duration']);
                // $model = $this->_giftuser->create->load($item['giftuserid']);
                if ($isExpire) {
                    // $model->setIsExpire(0);
                    $item['is_expire'] = '<div style="background:#f9d4d4;border:1px solid;border-color:#e22626;padding: 0 7px;text-align:center;text-transform: uppercase;color:#e22626;font-weight:bold;" title="Gift Card is expire">Expired</div>';
                } else {
                    // $model->setIsExpire(1);
                    $item['is_expire'] = '<div style="background:#d0e5a9;border:1px solid;border-color:#5b8116;padding: 0 7px;text-align:center;text-transform: uppercase;color:#185b00;font-weight:bold;" title="Gift Card is active">Active</div>';
                }
            }
        }
        return $dataSource;
    }
}
