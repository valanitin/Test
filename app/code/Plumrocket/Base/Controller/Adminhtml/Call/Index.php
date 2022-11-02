<?php
/**
 * @package     Plumrocket_Base
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license/  End-user License Agreement
 */

namespace Plumrocket\Base\Controller\Adminhtml\Call;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Plumrocket\Base\Api\GetModuleVersionInterface;
use Plumrocket\Base\Model\External\Urls;
use Plumrocket\Base\Model\Utils\GetEnabledStoresUrls;

class Index extends Action
{
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    private $resultJsonFactory;

    /**
     * @var \Magento\Framework\App\Config\Storage\WriterInterface
     */
    private $configWriter;

    /**
     * @var \Magento\Framework\App\ProductMetadataInterface
     */
    private $productMetadata;

    /**
     * @var \Plumrocket\Base\Api\GetModuleVersionInterface
     */
    private $getModuleVersion;

    /**
     * @var \Plumrocket\Base\Model\Utils\GetEnabledStoresUrls
     */
    private $getEnabledStoresUrls;

    /**
     * @param \Magento\Backend\App\Action\Context                   $context
     * @param \Magento\Framework\Controller\Result\JsonFactory      $resultJsonFactory
     * @param \Magento\Framework\App\Config\Storage\WriterInterface $configWriter
     * @param \Magento\Framework\App\ProductMetadataInterface       $productMetadata
     * @param \Plumrocket\Base\Api\GetModuleVersionInterface        $getModuleVersion
     * @param \Plumrocket\Base\Model\Utils\GetEnabledStoresUrls     $getEnabledStoresUrls
     */
    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        WriterInterface $configWriter,
        ProductMetadataInterface $productMetadata,
        GetModuleVersionInterface $getModuleVersion,
        GetEnabledStoresUrls $getEnabledStoresUrls
    ) {
        parent::__construct($context);
        $this->productMetadata = $productMetadata;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->configWriter = $configWriter;
        $this->getModuleVersion = $getModuleVersion;
        $this->getEnabledStoresUrls = $getEnabledStoresUrls;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Json|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        $return = [];

        if (isset($data['order_id'], $data['account_email'], $data['module'])) {
            $moduleName = $data['module'];

            $postData = [
                'order' => $data['order_id'],
                'email' => $data['account_email'],
                'name_version' => $this->getModuleVersion->execute($moduleName),
                'base_urls' => $this->getEnabledStoresUrls->execute(),
                'name' => $moduleName,
                'edition' => $this->productMetadata->getEdition(),
                'platform' => 'm2',
                'pixel' => 0,
                'v' => 1,
            ];

            $response = $this->call($postData);
            $response = (array)json_decode($response);

            if (!empty($response['hash'])) {
                $this->configWriter->save(
                    $moduleName.'/module/data',
                    $response['hash'],
                    ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
                    0
                );
                $return['hash'] = true;
            } else {
                $return['hash'] = false;
            }

            if (isset($response['data'])) {
                $return['data'] = $response['data'];
            }

            if (!empty($response['errors'])) {
                if (is_array($response['errors'])) {
                    $error = implode('<br />', $response['errors']);
                } else {
                    $error = $response['errors'];
                }
                $return['error'] = $error;
            }
        }

        $resultJson = $this->resultJsonFactory->create();

        return $resultJson->setData(json_encode($return));
    }

    /**
     * @param $postData
     * @return bool|string
     */
    private function call($postData)
    {
        $url = 'https://' . Urls::MARKETPLACE_PINGBACK_URL;

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, 1);
        if (is_array($postData)) {
            $postData = http_build_query($postData);
        }
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }

    /**
     * @return bool
     */
    public function _processUrlKeys()
    {
        return true;
    }

    /**
     * Check Permission.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return true;
    }
}
