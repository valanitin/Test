<?php
/**
 * Review renderer
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Plumrocket\Amp\Block\Review\Product;

class ReviewRenderer extends \Magento\Review\Block\Product\ReviewRenderer
{
    /**
     * Array of available template name
     *
     * @var array
     */
    protected $_availableTemplates = [
        self::FULL_VIEW => 'Plumrocket_Amp::review/helper/summary.phtml',
        self::SHORT_VIEW => 'Plumrocket_Amp::review/helper/summary_short.phtml',
    ];
}
