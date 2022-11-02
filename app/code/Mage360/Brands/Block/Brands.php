<?php
declare(strict_types=1);
/**
 * Mage360_Brands extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category  Mage360
 * @package   Mage360_Brands
 * @copyright 2018 Mage360
 * @license   http://opensource.org/licenses/mit-license.php MIT License
 * @author    Qaiser Bashir
 */

namespace Mage360\Brands\Block;

use Magento\Backend\Block\Template\Context;
use Mage360\Brands\Model\Brands as BrandsModel;
use Mage360\Brands\Model\ResourceModel\Brands\CollectionFactory as BrandsCollectionFactory;
use Mage360\Brands\Model\ResourceModel\Brands\Collection;
use Magento\Store\Model\ScopeInterface;

class Brands extends \Magento\Framework\View\Element\Template
{

    /**
     * @var BrandsCollectionFactory
     */
    public $brandsCollectionFactory;
    public $allChars = array();

    public function __construct(
        BrandsCollectionFactory $brandsCollectionFactory,
        Context $context,
        array $data = []
    ) {
        $this->brandsCollectionFactory = $brandsCollectionFactory;
        parent::__construct($context, $data);
    }

    /**
     * return brands collection
     *
     * @return CollectionFactory
     */
    public function getBrands()
    {
        $collection = $this->brandsCollectionFactory->create()
            ->addFieldToSelect('*')
            ->addFieldToFilter('is_active', 1)
            ->setOrder('name', 'ASC');
        
        $charBarndArray = array();
        foreach($collection as $brand)
		{	
			$name = trim($brand->getName());
			$charBarndArray[strtoupper($name[0])][] = $brand;
            $this->allChars[strtoupper($name[0])] = strtoupper($name[0]);
		}
        
        
        return $charBarndArray;
    }
    
    /**
     * return brands collection
     *
     * @return CollectionFactory
     */
    public function getFeaturedBrands()
    {
        $collection = $this->brandsCollectionFactory->create()
            ->addFieldToSelect('*')
            ->addFieldToFilter('is_featured', 1)
            ->addFieldToFilter('is_active', 1)
            ->setOrder('name', 'ASC');
        return $collection;
    }
    public function getAllChars(){
        return $this->allChars;
    }
}
