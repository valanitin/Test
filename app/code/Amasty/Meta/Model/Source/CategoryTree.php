<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Meta
 */


declare(strict_types=1);

namespace Amasty\Meta\Model\Source;

class CategoryTree implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * @var \Amasty\Meta\Helper\Data
     */
    private $dataHelper;

    public function __construct(\Amasty\Meta\Helper\Data $dataHelper)
    {
        $this->dataHelper = $dataHelper;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return $this->dataHelper->getTree();
    }
}
