<?php

namespace Dynamic\CustomerDataSync\Model\Entity\Attribute\Source\AbstractSource;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;

class Options extends AbstractSource
{
    /**
    * Retrieve All options
    *
    * @return array
    */
    public function getAllOptions()
    {
        $this->_options = [
            ['label'=> "Pending", 'value' => 1],
            ['label'=> "Completed", 'value' => 2],
        ];

        return $this->_options;
    }
}