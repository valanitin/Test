<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Meta
 */


declare(strict_types=1);

namespace Amasty\Meta\Block\Adminhtml\Edit;

use Amasty\Meta\Api\Data\ConfigInterface;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class DeleteButton implements ButtonProviderInterface
{
    /**
     * Url Builder
     *
     * @var \Magento\Framework\UrlInterface
     */
    private $urlBuilder;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;

    public function __construct(
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Framework\App\RequestInterface $request
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->request = $request;
    }

    /**
     * {@inheritdoc}
     */
    public function getButtonData()
    {
        $data = [];
        $configId = $this->request->getParam(ConfigInterface::CONFIG_ID);
        if ($configId) {
            $data = [
                'label' => __('Delete'),
                'class' => 'delete',
                'on_click' => sprintf(
                    "deleteConfirm('%s','%s')",
                    __('Are you sure you want to delete template?'),
                    $this->urlBuilder->getUrl(
                        '*/*/delete',
                        [ConfigInterface::CONFIG_ID => $configId]
                    )
                ),
                'sort_order' => 20,
            ];
        }

        return $data;
    }
}
