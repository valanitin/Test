<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ShopbySeo
 */


namespace Amasty\ShopbySeo\Plugin\Framework\View\Page;

use \Magento\Framework\View\Page\Config as NativeConfig;

class Config
{
    const NOINDEX = 'NOINDEX';

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;

    public function __construct(
        \Magento\Framework\App\RequestInterface $request
    ) {
        $this->request = $request;
    }

    /**
     * @param NativeConfig $subject
     * @param $result
     * @return string
     */
    public function afterGetRobots(
        NativeConfig $subject,
        $result
    ) {
        if ($this->request->getModuleName() === 'catalogsearch') {
            $robots = explode(',', $result);
            $robots[0] = self::NOINDEX;
            $result = implode(',', $robots);
        }

        return $result;
    }
}
