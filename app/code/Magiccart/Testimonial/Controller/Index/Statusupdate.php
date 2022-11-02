<?php
/**
 * Magiccart 
 * @category    Magiccart 
 * @copyright   Copyright (c) 2014 Magiccart (http://www.magiccart.net/) 
 * @license     http://www.magiccart.net/license-agreement.html
 * @Author: DOng NGuyen<nguyen@dvn.com>
 * @@Create Date: 2019-05-27 08:40:51
 * @@Modify Date: 2019-05-27 08:48:48
 * @@Function:
 */

namespace Magiccart\Testimonial\Controller\Index;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Controller\ResultFactory;

class Statusupdate extends \Magento\Framework\App\Action\Action
{
    
    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var Context
     */
    private $context;
    /**
     * @var LoggerInterface
     */
    private $logger;
    
    protected $_moduleHelper;

    /**
     * @param Context $context
     * @param DataPersistorInterface $dataPersistor
     * @param LoggerInterface $logger
  
     */
    
    public function __construct(
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Magento\Framework\App\Action\Context $context,
        \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory,
        \Magiccart\Testimonial\Helper\Data $moduleHelper
    ) {
        $this->_fileUploaderFactory = $fileUploaderFactory;
        $this->_moduleHelper = $moduleHelper;
        parent::__construct($context);
    }
    
    public function execute()
    {
       		if(array_key_exists('platform_id',$_REQUEST))
			{
                $model = $this->_objectManager->create('Magiccart\Testimonial\Model\Testimonial');
                try {
                    
                      
					$model->load($_REQUEST['platform_id']);
                    $model->setStatus($_REQUEST['status']);
                    $model->save();
                    
					
                } catch (\Exception $e) {
                    if ($e->getCode() == 0) {
                        $this->messageManager->addError($e->getMessage());
                    }
                }
           
			}
    }
}