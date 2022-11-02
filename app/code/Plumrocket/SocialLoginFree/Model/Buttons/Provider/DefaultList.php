<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2019 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

namespace Plumrocket\SocialLoginFree\Model\Buttons\Provider;

class DefaultList implements \Plumrocket\SocialLoginFree\Api\Buttons\ProviderInterface
{
    /**
     * @var \Plumrocket\SocialLoginFree\Model\Buttons\Factory
     */
    private $buttonsFactory;

    /**
     * @var \Plumrocket\SocialLoginFree\Api\TypesProviderInterface
     */
    private $typesProvider;

    /**
     * @var \Plumrocket\SocialLoginFree\Model\Buttons\Preparer
     */
    private $preparer;

    /**
     * @var array[]
     */
    private $buttonsCache = [];

    /**
     * @var \Plumrocket\SocialLoginFree\Helper\Config
     */
    private $config;

    /**
     * DefaultList constructor.
     *
     * @param \Plumrocket\SocialLoginFree\Model\Buttons\Factory      $buttonsFactory
     * @param \Plumrocket\SocialLoginFree\Api\TypesProviderInterface $typesProvider
     * @param \Plumrocket\SocialLoginFree\Model\Buttons\Preparer     $preparer
     * @param \Plumrocket\SocialLoginFree\Helper\Config              $config
     */
    public function __construct(
        \Plumrocket\SocialLoginFree\Model\Buttons\Factory $buttonsFactory,
        \Plumrocket\SocialLoginFree\Api\TypesProviderInterface $typesProvider,
        \Plumrocket\SocialLoginFree\Model\Buttons\Preparer $preparer,
        \Plumrocket\SocialLoginFree\Helper\Config $config
    ) {
        $this->buttonsFactory = $buttonsFactory;
        $this->typesProvider = $typesProvider;
        $this->preparer = $preparer;
        $this->config = $config;
    }

    /**
     * {@inheritDoc}
     */
    public function getButtons($onlyEnabled = true, $storeId = null, $forceReload = false)
    {
        $cacheKey = $this->getCacheKey([$onlyEnabled, $storeId]);

        if ($forceReload || ! isset($this->buttonsCache[$cacheKey])) {
            $types = $onlyEnabled
                ? $this->typesProvider->getEnabledList($storeId)
                : $this->typesProvider->getAll($storeId);

            $this->buttonsCache[$cacheKey] = $this->buttonsFactory->create($types);
        }

        return $this->buttonsCache[$cacheKey];
    }

    /**
     * {@inheritDoc}
     */
    public function getPreparedButtons(
        $onlyEnabled = true,
        $splitByVisibility = true,
        $storeId = null,
        $forceReload = false
    ) {
        $buttons = $this->getButtons($onlyEnabled, $storeId, $forceReload);

        return $this->preparer->prepareSortAndVisibility(
            $buttons,
            $this->config->getSortableParams(),
            $splitByVisibility
        );
    }

    /**
     * Get key for cache
     *
     * @param array $data
     * @return string
     */
    private function getCacheKey($data)
    {
        $serializeData = [];
        foreach ($data as $key => $value) {
            if (is_object($value)) {
                $serializeData[$key] = $value->getId();
            } else {
                $serializeData[$key] = $value;
            }
        }
        $serializeData = json_encode($serializeData);
        return sha1($serializeData);
    }
}
