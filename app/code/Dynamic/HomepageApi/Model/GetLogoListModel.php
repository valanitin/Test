<?php

namespace Dynamic\HomepageApi\Model;

use Dynamic\HomepageApi\Api\GetLogoList;

class GetLogoListModel implements GetLogoList
{
    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $jsonHelper;

    /**
     * @var \Magento\Theme\Block\Html\Header\Logo
     */
    protected $_logo;

    /**
     * @var \Magento\Store\Model\App\Emulation
     */
    protected $appEmulation;

    /**
     * Logo data constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Theme\Block\Html\Header\Logo $logo
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Theme\Block\Html\Header\Logo $logo,
        \Magento\Store\Model\App\Emulation $appEmulation,
        \Magento\Framework\Json\Helper\Data $jsonHelper
    ) {
        $this->jsonHelper = $jsonHelper;
        $this->_logo = $logo;
        $this->appEmulation = $appEmulation;
    }

    /**
     * Returns logo data
     *
     * @api
     * @return return logo array collection.
     */
    public function getList()
    {
        $data = [];

        $this->appEmulation->startEnvironmentEmulation(1, \Magento\Framework\App\Area::AREA_FRONTEND, true);

        $logoUrl = $this->getLogoSrc();

        if(!$logoUrl) {
            $errorMsg[] = array(
                ['status' => 'No Logo','message' => __('There are no Logo in this website.') ]
            );
            return $errorMsg;
        } else {
            $data[] = [
                "logo_url" => $this->getLogoSrc(),
                "logo_alt" => $this->getLogoAlt(),
                "logo_width" => $this->getLogoWidth(),
                "logo_height" => $this->getLogoHeight()
            ];
        }

        $this->appEmulation->stopEnvironmentEmulation();
        return $data;
    }

    /**
     * Get logo image URL
     *
     * @return string
     */
    public function getLogoSrc()
    {    
        return $this->_logo->getLogoSrc();
    }
    
    /**
     * Get logo text
     *
     * @return string
     */
    public function getLogoAlt()
    {    
        return $this->_logo->getLogoAlt();
    }
    
    /**
     * Get logo width
     *
     * @return int
     */
    public function getLogoWidth()
    {    
        return $this->_logo->getLogoWidth();
    }
    
    /**
     * Get logo height
     *
     * @return int
     */
    public function getLogoHeight()
    {    
        return $this->_logo->getLogoHeight();
    }  
}
