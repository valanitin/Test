<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

namespace Plumrocket\SocialLoginFree\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\FileSystemException;
use Plumrocket\SocialLoginFree\Model\Account\Debug\Logger as SocialDebugLogger;

/**
 * Download social log to giving it to tech engineer
 */
class Download extends Action
{
    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    private $fileFactory;

    /**
     * @var SocialDebugLogger
     */
    private $socialDebugLogger;

    /**
     * Download constructor.
     *
     * @param \Magento\Backend\App\Action\Context              $context
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     * @param SocialDebugLogger                                $socialDebugLogger
     */
    public function __construct(
        Context $context,
        FileFactory $fileFactory,
        SocialDebugLogger $socialDebugLogger
    ) {
        $this->fileFactory      = $fileFactory;
        parent::__construct($context);
        $this->socialDebugLogger = $socialDebugLogger;
    }

    public function execute()
    {
        $fileName = SocialDebugLogger::LOG_FILE;

        try {
            $absoluteFilePath = $this->socialDebugLogger->getAbsoluteFilePath();
            if (file_exists($absoluteFilePath) && file_get_contents($absoluteFilePath) !== '') {
                $content = [
                    'type' => 'filename',
                    'value' => $this->socialDebugLogger->getRelativeFilePath(),
                    'rm' => false
                ];

                $this->fileFactory->create($fileName, $content);
                $result = $this->resultFactory->create(ResultFactory::TYPE_RAW);
            } else {
                $this->messageManager->addErrorMessage(__('The log file is missing.'));
                $result = $this->resultRedirectFactory->create();
                $result->setUrl($this->_redirect->getRefererUrl());
            }
        } catch (FileSystemException $e) {
            $this->messageManager->addExceptionMessage($e);
            $result = $this->resultRedirectFactory->create();
            $result->setUrl($this->_redirect->getRefererUrl());
        }

        return $result;
    }
}
