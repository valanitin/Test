<?php
/**
 * @package     Plumrocket_Base
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license/  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\Base\Model\Statistic\Usage\Config;

use Magento\Config\Model\Config\Structure\Element\Field;
use Magento\Config\Model\Config\Structure\Element\Group;
use Magento\Config\Model\Config\Structure\Element\Section;
use Magento\Framework\App\Area;
use Magento\Framework\App\Config\Initial\Reader;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\State;
use Magento\Framework\Config\ScopeInterface;
use Magento\Framework\DataObject;
use Magento\Framework\DataObjectFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\State\InvalidTransitionException;
use Plumrocket\Base\Model\Extensions\Information;

/**
 * @since 2.3.0
 */
class Collector
{
    const ATTRIBUTE_USE_IN_STATISTIC = 'pr_use_in_statistic';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var \Plumrocket\Base\Model\Statistic\Usage\Config\GetConfigStructure
     */
    private $getConfigStructure;

    /**
     * @var \Magento\Framework\Config\ScopeInterface
     */
    private $scope;

    /**
     * @var \Magento\Framework\App\State
     */
    private $state;

    /**
     * @var \Magento\Framework\App\Config\Initial\Reader
     */
    private $configReader;

    /**
     * @var \Magento\Framework\DataObjectFactory
     */
    private $dataObjectFactory;

    /**
     * @var \Magento\Framework\DataObject|null
     */
    protected $defaultConfig;

    /**
     * @var string
     */
    protected $configSection;

    /**
     * Collector constructor.
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface               $scopeConfig
     * @param \Plumrocket\Base\Model\Statistic\Usage\Config\GetConfigStructure $getConfigStructure
     * @param \Magento\Framework\Config\ScopeInterface                         $scope
     * @param \Magento\Framework\App\State                                     $state
     * @param \Magento\Framework\App\Config\Initial\Reader                     $configReader
     * @param \Magento\Framework\DataObjectFactory                             $dataObjectFactory
     * @param string                                                           $configSection
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        GetConfigStructure $getConfigStructure,
        ScopeInterface $scope,
        State $state,
        Reader $configReader,
        DataObjectFactory $dataObjectFactory,
        string $configSection = Information::CONFIG_SECTION
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->getConfigStructure = $getConfigStructure;
        $this->scope = $scope;
        $this->state = $state;
        $this->configReader = $configReader;
        $this->dataObjectFactory = $dataObjectFactory;
        $this->configSection = $configSection;
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\State\InvalidTransitionException
     */
    public function collect(): array
    {
        $currentScope = $this->scope->getCurrentScope();

        $data = [];

        try {
            $data = $this->state->emulateAreaCode(Area::AREA_ADMINHTML, function () {
                $this->scope->setCurrentScope(Area::AREA_ADMINHTML);

                return $this->collectConfigs();
            });

            $this->scope->setCurrentScope($currentScope);
        } catch (\Exception $e) {
            throw new InvalidTransitionException(__('%1', $e->getMessage()), $e);
        } finally {
            $this->scope->setCurrentScope($currentScope);
        }

        return $data;
    }

    /**
     * @return array
     */
    public function collectConfigs(): array
    {
        $configData = [];
        /**
         * Use factory (getConfigStructure) in order to initialize object after code area emulated
         * @var \Magento\Config\Model\Config\Structure\Element\Section $section
         */
        $section = $this->getConfigStructure->execute()->getElement($this->configSection);
        /** @var \Magento\Config\Model\Config\Structure\Element\Group $group */
        foreach ($section->getChildren() as $group) {
            /** @var \Magento\Config\Model\Config\Structure\Element\Field $field */
            foreach ($group->getChildren() as $field) {
                if ($field->getAttribute(self::ATTRIBUTE_USE_IN_STATISTIC)) {
                    $configData[$field->getPath()] = $this->proccesField($section, $group, $field);
                }
            }
        }

        return $configData;
    }

    /**
     * [
     *      'label' => '',
     *      'value' => '',
     *      'options' => [
     *          ['value' => value_1, 'label' => label_1],
     *          ['value' => value_1, 'label' => label_1],
     *      ],
     *      'default' => ''
     * ]
     *
     * @param \Magento\Config\Model\Config\Structure\Element\Section $section
     * @param \Magento\Config\Model\Config\Structure\Element\Group   $group
     * @param \Magento\Config\Model\Config\Structure\Element\Field   $field
     * @return array
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function proccesField(
        Section $section,
        Group $group,
        Field $field
    ) {
        $fieldData = [
            'label' => $field->getLabel(),
            'value' => $this->anonymizeData($field, 'value', $this->scopeConfig->getValue($field->getPath())),
            'default' => $this->getDefaultConfig()->getDataByPath($field->getPath()),
        ];

        if (in_array($field->getType(), ['select', 'multiselect'], true)) {
            $fieldData['options'] = $this->prepareOptions(
                $this->anonymizeData($field, 'options', $field->getOptions())
            );
        }

        return $fieldData;
    }

    /**
     * @return \Magento\Framework\DataObject
     */
    protected function getDefaultConfig(): DataObject
    {
        if (null === $this->defaultConfig) {
            try {
                $config = $this->configReader->read();
                $moduleConfig = $config['data']['default'][$this->configSection] ?? [];
            } catch (LocalizedException $e) {
                $moduleConfig = [];
            }

            $this->defaultConfig = $this->dataObjectFactory->create(
                ['data' => [$this->configSection => $moduleConfig]]
            );
        }

        return $this->defaultConfig;
    }

    /**
     * @param array $options
     * @return array
     */
    protected function prepareOptions(array $options) : array
    {
        return array_map(
            static function ($option) {
                $option['label'] = (string) $option['label'];
                return $option;
            },
            $options
        );
    }

    /**
     * @param \Magento\Config\Model\Config\Structure\Element\Field $field
     * @param string                                               $type
     * @param                                                      $currentValue
     * @return array|mixed|string
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function anonymizeData(Field $field, string $type, $currentValue)
    {
        return $currentValue;
    }
}
