<?php
namespace Ethnic\Customfooter\Model\ResourceModel;

use Magento\Framework\Model\AbstractModel;

class Allnews extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
	public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context
    ) {
        parent::__construct($context);
    }
	
	/**
     * Define main table
     */
	protected function _construct()
	{
		$this->_init('ethnic_customfooter', 'id');
	}
	// protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
 //    {
 //        $object->setUpdatedAt($this->_date->date());
 //        if ($object->isObjectNew()) {
 //            $object->setCreatedAt($this->_date->date());
 //        }
 //        return parent::_beforeSave($object);
 //    }

}
?>