<?php

declare(strict_types=1);

namespace Sololuxary\TrackTickets\Block;

use Dynamic\Mytickets\Model\ResourceModel\Mytickets\Collection;
use Dynamic\Mytickets\Model\ResourceModel\Mytickets\CollectionFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

/**
 * class Myticketslist
 */
class Myticketslist extends Template
{
    /**
     * @var CollectionFactory
     */
    protected $_myticketsollectionFactory;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @param Context $context
     * @param CollectionFactory $myticketsollectionFactory
     * @param RequestInterface $request
     */
    public function __construct(
        Context $context,
        CollectionFactory $myticketsollectionFactory,
        RequestInterface $request
    ) {
        $this->_myticketsollectionFactory = $myticketsollectionFactory;
        $this->request = $request;
        parent::__construct($context);
    }

    /**
     * @return Collection|null
     */
    public function getMyTicketsCollection()
    {
        $customerEmail = $this->request->getPostValue('email');
        if ($customerEmail) {
            return $this->_myticketsollectionFactory->create()->addFieldToFilter('email',
                array('eq' => $customerEmail));
        }
        return null;
    }
}
