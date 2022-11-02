<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Meta
 */


declare(strict_types=1);

namespace Amasty\Meta\Plugin\ShopbyBrand\Controller;

use Amasty\Meta\Model\Registry;
use Amasty\ShopbyBrand\Controller\Router;
use Magento\Framework\App\ActionInterface;

class RouterPlugin
{
    const IS_BRAND_PAGE = 'is_brand_page';

    /**
     * @var Registry
     */
    private $registry;

    public function __construct(
        Registry $registry
    ) {
        $this->registry = $registry;
    }

    public function afterMatch(Router $subject, ?ActionInterface $result): ?ActionInterface
    {
        if ($result) {
            $this->registry->set(self::IS_BRAND_PAGE, true);
        }

        return $result;
    }
}
