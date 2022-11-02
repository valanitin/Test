<?php
declare(strict_types=1);

namespace Magentizer\GeoIP\Block\Adminhtml\Form\Field;

use Magento\Framework\View\Element\Html\Select;

class StoreColumn extends Select
{
public function getStoreGroups()
{
    $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    $_storeManager = $objectManager->create('Magento\Store\Model\StoreManagerInterface');
    $_websites = $_storeManager->getWebsites();
    $storeName = [];
    foreach($_websites as $website){
       $groups = $website->getGroups();
    
    foreach ($groups as $key => $group) {
        $storeName[$key] = $group->getName();
    }
    
    }
    return $storeName; 
}
    
    
    /**
     * Set "name" for <select> element
     *
     * @param string $value
     * @return $this
     */
    public function setInputName($value)
    {
        return $this->setName($value);
    }

    /**
     * Set "id" for <select> element
     *
     * @param $value
     * @return $this
     */
    public function setInputId($value)
    {
        return $this->setId($value);
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    public function _toHtml(): string
    {
        if (!$this->getOptions()) {
            $this->setOptions($this->getSourceOptions());
        }
        return parent::_toHtml();
    }

    private function getSourceOptions(): array
    {
        $groups = $this->getStoreGroups();
        $returnGroups = [];
        
        foreach($groups as $groupid => $groupName){
            $returnGroups[] = ['label' => $groupName, 'value' => $groupid];
        }
        
        return $returnGroups;
    }
}