<?php
namespace Dynamic\OrderErpSync\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
  * Class OptionErpSync
  */
class OptionErpSync implements OptionSourceInterface
{
    /**
     * Array
     *
     * @var array
     */
    protected $options;
    /**
     * Array
     *
     * @return array
     */
    public function toOptionArray()
    {
        $typesOfErpSync = [
            0 => 'Pending',
            1 => 'Completed',
            2 => 'Failed'
        ];
        $options = [];
        foreach ($typesOfErpSync as $key => $typeOfErpSync) {
            $options[] = [
                'label' => $typeOfErpSync,
                'value' => $key
            ];
        }
    
        return $options;
    }
}