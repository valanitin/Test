<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\SocialLoginFree\Model\Statistic\Usage\Config;

use Magento\Config\Model\Config\Structure\Element\Field;
use Magento\Framework\App\Config\Initial\Reader;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\State;
use Magento\Framework\Config\ScopeInterface;
use Magento\Framework\DataObjectFactory;
use Plumrocket\Base\Model\Statistic\Usage\Config\GetConfigStructure;
use Plumrocket\SocialLoginFree\Model\Information;

/**
 * @since 2.3.6
 */
class Collector extends \Plumrocket\Base\Model\Statistic\Usage\Config\Collector
{
    /**
     * @var \Plumrocket\SocialLoginFree\Model\Statistic\Usage\Config\Anonymize
     */
    private $anonymize;

    /**
     * Collector constructor.
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface           $scopeConfig
     * @param \Plumrocket\Base\Model\Statistic\Usage\Config\GetConfigStructure   $getConfigStructure
     * @param \Magento\Framework\Config\ScopeInterface                     $scope
     * @param \Magento\Framework\App\State                                 $state
     * @param \Magento\Framework\App\Config\Initial\Reader                 $configReader
     * @param \Magento\Framework\DataObjectFactory                         $dataObjectFactory
     * @param \Plumrocket\SocialLoginFree\Model\Statistic\Usage\Config\Anonymize $anonymize
     * @param string                                                       $configSection
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        GetConfigStructure $getConfigStructure,
        ScopeInterface $scope,
        State $state,
        Reader $configReader,
        DataObjectFactory $dataObjectFactory,
        Anonymize $anonymize,
        string $configSection = Information::CONFIG_SECTION
    ) {
        parent::__construct(
            $scopeConfig,
            $getConfigStructure,
            $scope,
            $state,
            $configReader,
            $dataObjectFactory,
            $configSection
        );
        $this->anonymize = $anonymize;
    }

    /**
     * @param \Magento\Config\Model\Config\Structure\Element\Field $field
     * @param string                                               $type
     * @param                                                      $currentValue
     * @return array|mixed|string
     */
    protected function anonymizeData(Field $field, string $type, $currentValue)
    {
        if ($field->getAttribute('pl_stat_anonymize_cms')) {
            if ('value' === $type) {
                return $this->anonymize->cmsPageValue($currentValue);
            }

            if ('options' === $type) {
                return $this->anonymize->cmsPageOptions($currentValue);
            }
        }

        return $currentValue;
    }
}
