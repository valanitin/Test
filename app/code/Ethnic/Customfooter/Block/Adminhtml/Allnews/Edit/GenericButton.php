<?php
namespace Ethnic\Customfooter\Block\Adminhtml\Allnews\Edit;

use Magento\Backend\Block\Widget\Context;
use Ethnic\Customfooter\Model\AllnewsRepository;
use Magento\Framework\Exception\NoSuchEntityException;

class GenericButton
{
    protected $context;
   
    protected $allnewsRepository;
    
    public function __construct(
        Context $context,
        AllnewsRepository $allnewsRepository
    ) {
        $this->context = $context;
        $this->allnewsRepository = $allnewsRepository;
    }

    public function getNewsId()
    {
        try {
            return $this->allnewsRepository->getById(
                $this->context->getRequest()->getParam('id')
            )->getId();
        } catch (NoSuchEntityException $e) {
        }
        return null;
    }

    public function getUrl($route = '', $params = [])
    {
        return $this->context->getUrlBuilder()->getUrl($route, $params);
    }
}
?>
