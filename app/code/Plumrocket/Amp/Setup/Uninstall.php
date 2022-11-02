<?php
/**
 * Plumrocket Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End-user License Agreement
 * that is available through the world-wide-web at this URL:
 * http://wiki.plumrocket.net/wiki/EULA
 * If you are unable to obtain it through the world-wide-web, please
 * send an email to support@plumrocket.com so we can send you a copy immediately.
 *
 * @package     Plumrocket Amp v2.x.x
 * @copyright   Copyright (c) 2016 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Amp\Setup;

/* Uninstall Amp */
class Uninstall extends \Plumrocket\Base\Setup\AbstractUninstall
{
    protected $_configSectionId = 'pramp';
    protected $_cmsBlocks = ['amp_footer_links'];
    protected $_attributes = [
        \Magento\Catalog\Model\Category::ENTITY => ['amp_homepage_image'],
    ];
    protected $_pathes = ['/app/code/Plumrocket/Amp'];
}
