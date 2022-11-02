<?php
/**
 * @package     Plumrocket_Base
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license/  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\Base\Controller\Adminhtml\Debug;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Plumrocket\Base\Model\Debug\Export as ExportDebugInfo;

/**
 * @since 2.3.0
 */
class Download extends Action
{
    /**
     * @var ExportDebugInfo
     */
    private $exportDebugInfo;

    /**
     * Download constructor.
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Plumrocket\Base\Model\Debug\Export $exportDebugInfo
     */
    public function __construct(
        Context $context,
        ExportDebugInfo $exportDebugInfo
    ) {
        parent::__construct($context);
        $this->exportDebugInfo = $exportDebugInfo;
    }

    /**
     * Execute export action.
     *
     * @return \Magento\Framework\Controller\ResultInterface
     * @throws \Exception
     */
    public function execute() : ResultInterface
    {
        try {
            $this->exportDebugInfo->execute();
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        return $this->resultFactory->create(ResultFactory::TYPE_RAW);
    }
}
