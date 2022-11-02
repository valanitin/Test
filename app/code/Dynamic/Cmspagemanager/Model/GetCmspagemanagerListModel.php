<?php

namespace Dynamic\Cmspagemanager\Model;

use Dynamic\Cmspagemanager\Api\GetCmspagemanager;

class GetCmspagemanagerListModel implements GetCmspagemanager
{
    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $jsonHelper;

    /**
     * @var \Dynamic\Cmspagemanager\Model\Cmspagemanager
     */
    protected $modelCmspagemanager;

    /**
     * Banner data constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Dynamic\Cmspagemanager\Model\Cmspagemanager $modelCmspagemanager
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Dynamic\Cmspagemanager\Model\Cmspagemanager $modelCmspagemanager,
        \Magento\Framework\Json\Helper\Data $jsonHelper
    ) {
        $this->jsonHelper = $jsonHelper;
        $this->modelCmspagemanager = $modelCmspagemanager;
    }

    /**
     * Returns cms data
     *
     * @api
     * @return return cms array collection.
     */
    public function getCmspagemanagerList($pageId)
    {
        $data = [];
        
        $cmsCollection = $this->modelCmspagemanager->getCollection()->addFieldToFilter('cms_id', array('eq' => $pageId))->addFieldToFilter('status', array('eq' => 1));

        if(count($cmsCollection) > 0 && !empty($cmsCollection)) {

            foreach ($cmsCollection as $cmspage) {
                $data[] = [
                    "cmspagemanager_id" => $cmspage->getCmspagemanagerId(),
                    "cms_id" => $cmspage->getCmsId(),
                    "status" => $cmspage->getStatus(),
                    "extra_data" => $cmspage->getExtraData(),
                    "cms_text" => $this->getCmsArr($cmspage->getCmsText()),
                    "created_at" => $cmspage->getCreatedAt(),
                ];
            }

        } else {
            $data = array(
                ['status' => 'No Data','message' => __('There are no CMS data in this website.') ]
            );
        }
        
        return $data;
    }

    /**
    * Returns cms data array
    *
    * @api
    * @param  array $cmsData.
    * @return array cmsdata array.
    */
    public function getCmsArr($cmsData) {

        $cmsArr = [];

        if($cmsData) {
            $cmsArrData = json_decode($cmsData, true);
            if(count($cmsArrData) > 0 && !empty($cmsArrData)) {
                foreach ($cmsArrData as $data) {
                    $cmsArr[] = [
                        "title" => $data["title"],
                        "sub_title" => $data["sub_title"],
                        "description" => $data["description"]
                    ];
                }
            }   
        }
        return $cmsArr;
    }
}
