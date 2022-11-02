<?php

namespace LuxuryUnlimited\Coupon\Controller\Coupon;

use Magento\Framework\App\Action\Action;
use LuxuryUnlimited\Coupon\Model\Rule\Collection;
use Magento\Framework\App\Action\Context;

class Index extends Action
{
    /**
     * @codeCoverageIgnore
     */
    public function __construct(
        Context $context,
        Collection $collection
    ) {
        parent::__construct($context);
        $this->collection = $collection;
    }
    
    public function execute()
    {
        $this->_view->loadLayout();
        $this->_view->renderLayout();
    }
}