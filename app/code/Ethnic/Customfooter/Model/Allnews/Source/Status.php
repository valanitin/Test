<?php

namespace Ethnic\Customfooter\Model\Allnews\Source;

use Magento\Framework\Data\OptionSourceInterface;

class Status implements OptionSourceInterface
{
    protected $allNews;

    public function __construct(\Ethnic\Customfooter\Model\Allnews $allNews)
    {
        $this->allNews = $allNews;
    }

    public function toOptionArray()
    {
        $availableOptions = $this->allNews->getAvailableStatuses();
        $options = [];
        foreach ($availableOptions as $key => $value) {
            $options[] = [
                'label' => $value,
                'value' => $key,
            ];
        }
        return $options;
    }
}
?>