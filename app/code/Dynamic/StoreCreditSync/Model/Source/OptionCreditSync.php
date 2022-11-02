<?php
namespace Dynamic\StoreCreditSync\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
  * Class OptionCreditSync
  */
class OptionCreditSync implements OptionSourceInterface
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
            2 => 'Failed',
            3 => 'Exclude'
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