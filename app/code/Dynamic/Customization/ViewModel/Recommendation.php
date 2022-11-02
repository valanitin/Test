<?php
/**
 * @author Raju Sadadiya
 * @package Dynamic_Customization
 */ 

namespace Dynamic\Customization\ViewModel;
 
use Magento\Framework\View\Element\Block\ArgumentInterface;

class Recommendation implements ArgumentInterface
{
    /**
     * @var \Dynamic\Customization\Helper\Data
     */
    protected $moduleHelper;
 
    public function __construct(
        \Dynamic\Customization\Helper\Data $moduleHelper
    ) {
        $this->moduleHelper = $moduleHelper;
    }

    public function getDynamicHelper()
    {
        return $this->moduleHelper;
    }
}
