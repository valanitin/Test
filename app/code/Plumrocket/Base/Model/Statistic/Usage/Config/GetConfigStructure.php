<?php
/**
 * @package     Plumrocket_Base
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license/  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\Base\Model\Statistic\Usage\Config;

use Magento\Config\Model\Config\Structure;

/**
 * @since 2.3.0
 */
class GetConfigStructure
{
    /**
     * @var \Magento\Config\Model\Config\StructureFactory
     */
    private $configStructureFactory;

    /**
     * @var \Magento\Config\Model\Config\Structure
     */
    private $configStructure;

    /**
     * GetConfigStructure constructor.
     *
     * @param \Magento\Config\Model\Config\StructureFactory $configStructureFactory
     */
    public function __construct(\Magento\Config\Model\Config\StructureFactory $configStructureFactory)
    {
        $this->configStructureFactory = $configStructureFactory;
    }

    /**
     * @return \Magento\Config\Model\Config\Structure
     */
    public function execute(): Structure
    {
        if (null === $this->configStructure) {
            $this->configStructure = $this->configStructureFactory->create();
        }

        return $this->configStructure;
    }
}
