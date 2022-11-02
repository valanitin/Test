<?php
namespace Ethnic\Customfooter\Model;

use Ethnic\Customfooter\Api\Data\AllnewsInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;

class Allnews extends AbstractModel implements AllnewsInterface, IdentityInterface
{
    const CACHE_TAG = 'ethnic_customfooter';

    //Unique identifier for use within caching
    protected $_cacheTag = self::CACHE_TAG;

    protected function _construct()
    {
        $this->_init('Ethnic\Customfooter\Model\ResourceModel\Allnews');
    }

    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    public function getDefaultValues()
    {
        $values = [];
        return $values;
    }

    public function getId()
    {
        return parent::getData(self::ID);
    }

    public function getEnabled()
    {
        return parent::getData(self::ENABLED);
    }

    public function getTitle()
    {
        return $this->getData(self::TITLE);
    }

    public function getCmsPageId()
    {
        return $this->getData(self::CMS_PAGE_ID);
    }

    public function getIsParent()
    {
        return $this->getData(self::IS_PARENT);
    }

    public function getParentId()
    {
        return $this->getData(self::PARENT_ID);
    }

    public function setId($id)
    {
        return $this->setData(self::ID, $id);
    }

     public function setEnabled($enabled)
    {
        return $this->setData(self::ENABLED, $enabled);
    }

    public function setTitle($title)
    {
        return $this->setData(self::TITLE, $title);
    }

    public function setCmsPageId($cms_page_id)
    {
        return $this->setData(self::CMS_PAGE_ID, $cms_page_id);
    }

    public function setIsParent($is_parent)
    {
        return $this->setData(self::IS_PARENT, $is_parent);
    }

    public function setParentId($parent_id)
    {
        return $this->setData(self::PARENT_ID, $parent_id);
    }

}
?>