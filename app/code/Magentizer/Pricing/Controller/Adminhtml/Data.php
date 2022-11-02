<?php
/*
 * Magentizer_Pricing

 * @category   SussexDev
 * @package    Magentizer_Pricing
 * @copyright  Copyright (c) 2019 Scott Parsons
 * @license    https://github.com/ScottParsons/module-sampleuicomponent/blob/master/LICENSE.md
 * @version    1.1.2
 */
namespace Magentizer\Pricing\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Magento\Backend\Model\View\Result\ForwardFactory;
use Magentizer\Pricing\Api\DataRepositoryInterface;

abstract class Data extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ACTION_RESOURCE = 'Magentizer_Pricing::data';

    /**
     * Data repository
     *
     * @var DataRepositoryInterface
     */
    protected $dataRepository;

    /**
     * Core registry
     *
     * @var Registry
     */
    protected $coreRegistry;

    /**
     * Result Page Factory
     *
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * Result Forward Factory
     *
     * @var ForwardFactory
     */
    protected $resultForwardFactory;
    
    public $cronFile;

    /**
     * Data constructor.
     *
     * @param Registry $registry
     * @param DataRepositoryInterface $dataRepository
     * @param PageFactory $resultPageFactory
     * @param ForwardFactory $resultForwardFactory
     * @param Context $context
     */
    public function __construct(
        Registry $registry,
        DataRepositoryInterface $dataRepository,
        PageFactory $resultPageFactory,
        ForwardFactory $resultForwardFactory,
        \Magentizer\Pricing\Cron\FetchPrices $cronFile,
        Context $context
    ) {
        $this->coreRegistry         = $registry;
        $this->dataRepository       = $dataRepository;
        $this->cronFile = $cronFile;
        $this->resultPageFactory    = $resultPageFactory;
        $this->resultForwardFactory = $resultForwardFactory;
        parent::__construct($context);
    }
}
