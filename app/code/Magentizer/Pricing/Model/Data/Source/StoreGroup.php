<?php
/*
 * Magentizer_Pricing

 * @category   SussexDev
 * @package    Magentizer_Pricing
 * @copyright  Copyright (c) 2019 Scott Parsons
 * @license    https://github.com/ScottParsons/module-sampleuicomponent/blob/master/LICENSE.md
 * @version    1.1.2
 */
namespace Magentizer\Pricing\Model\Data\Source;

use Magento\Framework\Data\OptionSourceInterface;

class StoreGroup implements OptionSourceInterface
{
    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $groups = $this->getStoreGroups();
        $returnGroups = [];
        
        foreach($groups as $groupid => $groupName){
            $returnGroups[] = ['label' => $groupName, 'value' => $groupid];
        }
        return $returnGroups;
    }
    
    public function getStoreGroups()
{
    $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    $_storeManager = $objectManager->create('Magento\Store\Model\StoreManagerInterface');
    $groups = $_storeManager->getWebsite()->getGroups();
    $storeName = [];
    foreach ($groups as $key => $group) {
        $storeName[$key] = $group->getName();
    }
    return $storeName;
}
}
