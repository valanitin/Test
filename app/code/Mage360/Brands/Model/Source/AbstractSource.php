<?php
declare(strict_types=1);
/**
 * Mage360_Storelocator extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 */
namespace Mage360\Brands\Model\Source;

use Magento\Framework\Option\ArrayInterface;

abstract class AbstractSource implements ArrayInterface
{
    /**
     * @var array
     */
    public $options;

    /**
     * @param array $options
     */
    public function __construct(
        array $options = []
    ) {
        $this->options = $options;
    }

    /**
     * @return array
     */
    abstract public function toOptionArray();

    /**
     * @param $value
     * @return string
     */
    public function getOptionText($value)
    {
        $options = $this->getOptions();
        if (!is_array($value)) {
            $value = explode(',', $value);
        }
        $texts = [];
        foreach ($value as $v) {
            if (isset($options[$v])) {
                $texts[] = $options[$v];
            }
        }
        return implode(', ', $texts);
    }
    /**
     * get options as key value pair
     *
     * @return array
     */
    public function getOptions()
    {
        $options = [];
        foreach ($this->toOptionArray() as $values) {
            $options[$values['value']] = __($values['label']);
        }
        return $options;
    }
}
