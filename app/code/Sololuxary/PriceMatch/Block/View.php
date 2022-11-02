<?php

declare(strict_types=1);

namespace Sololuxary\PriceMatch\Block;

use Magento\Backend\Block\Template\Context;
use Magento\Framework\View\Element\Template;
use Magento\Framework\App\Http\Context as HttpContext;

/**
 * class View
 */
class View extends Template
{
    /**
     * @var HttpContext
     */
    protected $httpContext;

    /**
     * @param Context $context
     * @param HttpContext $httpContext
     * @param array $data
     */
    public function __construct(
        Context $context,
        HttpContext $httpContext,
        array $data = []
    )
    {
        $this->httpContext = $httpContext;
        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    public function getAction(): string
    {
        return "<div class='message'>We have received your Request & We'll Get in Touch Soon. You can view responses on your email & under My Tickets.</div>";

    }

    /**
     * @return bool
     */
    public function isLoggedIn(): bool
    {
        return $this->httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH);
    }
}