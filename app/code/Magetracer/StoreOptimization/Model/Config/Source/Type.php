<?php
/**
 * Mage Tracer.
 *
 * @category  Magetracer
 * @package   Magetracer_StoreOptimization
 * @author    Magetracer
 * @copyright Copyright (c) Mage Tracer (https://www.magetracer.com)
 * @license   https://www.magetracer.com/license.html
 */

namespace Magetracer\StoreOptimization\Model\Config\Source;

/**
 * Option to choose compression type
 */
class Type
{

    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $manager;
    /**
     * Construct.
     *
     * @param \Magento\Catalog\Model\ProductTypes\ConfigInterface $config
     */
    public function __construct(
        \Magento\Framework\Module\Manager $manager
    ) {
        $this->manager = $manager;
    }
    /**
     * Options getter.
     *
     * @return array
     */
    public function toOptionArray()
    {
        $data = [
            ['value' => 'webp', 'label' => __('Webp Compression')],
            ['value' => 'jpeg', 'label' => __('JPEG Compression')]
        ];
        return $data;
    }
}
