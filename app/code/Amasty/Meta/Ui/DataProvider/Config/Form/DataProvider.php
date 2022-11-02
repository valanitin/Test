<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Meta
 */


declare(strict_types=1);

namespace Amasty\Meta\Ui\DataProvider\Config\Form;

use Amasty\Meta\Api\ConfigRepositoryInterface;
use Amasty\Meta\Api\Data\ConfigInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Amasty\Meta\Model\ResourceModel\Config\CollectionFactory;

class DataProvider extends AbstractDataProvider
{
    const AMMETA_CONFIG = 'ammeta_config';

    /**
     * @var ConfigRepositoryInterface
     */
    private $configRepository;

    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    public function __construct(
        CollectionFactory $collectionFactory,
        ConfigRepositoryInterface $configRepository,
        DataPersistorInterface $dataPersistor,
        $name,
        $primaryFieldName,
        $requestFieldName,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $collectionFactory->create();
        $this->configRepository = $configRepository;
        $this->dataPersistor = $dataPersistor;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * @return array
     * @throws NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getData()
    {
        $data = parent::getData();
        if ($data['totalRecords'] > 0) {
            if (isset($data['items'][0][ConfigInterface::CONFIG_ID])) {
                $configId = (int)$data['items'][0][ConfigInterface::CONFIG_ID];
                $config = $this->configRepository->getById($configId);
                $data = [$configId => $config->getData()];
            }
        }

        if ($savedData = $this->dataPersistor->get(self::AMMETA_CONFIG)) {
            $savedRedirectId = $savedData[ConfigInterface::CONFIG_ID] ?? null;
            $data[$savedRedirectId] = isset($data[$savedRedirectId])
                ? array_merge($data[$savedRedirectId], $savedData)
                : $savedData;
            $this->dataPersistor->clear(self::AMMETA_CONFIG);
        }

        return $data;
    }
}
