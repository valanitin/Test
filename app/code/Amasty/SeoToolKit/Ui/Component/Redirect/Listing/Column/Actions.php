<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_SeoToolKit
 */


declare(strict_types=1);

namespace Amasty\SeoToolKit\Ui\Component\Redirect\Listing\Column;

use Amasty\SeoToolKit\Api\Data\RedirectInterface;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;

class Actions extends Column
{
    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    public function __construct(
        UrlInterface $urlBuilder,
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource) : array
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $item[$this->getData('name')] = [
                    'edit'   => [
                        'href'  => $this->urlBuilder->getUrl(
                            'amasty_seotoolkit/redirect/edit',
                            [RedirectInterface::REDIRECT_ID => $item[RedirectInterface::REDIRECT_ID]]
                        ),
                        'label' => __('Edit'),
                    ],
                    'delete'   => [
                        'href'  => $this->urlBuilder->getUrl(
                            'amasty_seotoolkit/redirect/delete',
                            [RedirectInterface::REDIRECT_ID => $item[RedirectInterface::REDIRECT_ID]]
                        ),
                        'label' => __('Delete'),
                    ],
                ];
            }
        }

        return $dataSource;
    }
}
