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

use Magento\Framework\App\State;
use Magento\Cms\Model\BlockFactory;
use Magento\Cms\Model\PageFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface
{
    /**
     * Block Factory
     * @var \Magento\Cms\Model\BlockFactory
     */
    private $blockFactory;

    /**
     * Page Factory
     *
     * @var \Magento\Cms\Model\PageFactory
     */
    private $pageFactory;

    /**
     * @var \Plumrocket\Amp\Helper\Config
     */
    private $configHelper;

    /**
     * @var \Magento\Cms\Api\PageRepositoryInterface
     */
    private $pageRepository;

    /**
     * Init
     * @param \Magento\Cms\Model\BlockFactory $blockFactory and \Magento\Cms\Model\PageFactory $pageFactory
     * @param PageFactory $pageFactory
     * @param State $state
     * @param \Magento\Cms\Api\PageRepositoryInterface $pageRepository
     * @param \Plumrocket\Amp\Helper\Config $configHelper
     */
    public function __construct(
        \Magento\Cms\Model\BlockFactory $blockFactory,
        \Magento\Cms\Model\PageFactory $pageFactory,
        State $state,
        \Magento\Cms\Api\PageRepositoryInterface $pageRepository,
        \Plumrocket\Amp\Helper\Config $configHelper
    ) {
        $this->blockFactory = $blockFactory;
        $this->pageFactory = $pageFactory;
        $this->configHelper = $configHelper;
        $this->pageRepository = $pageRepository;

        try {
            $state->setAreaCode('adminhtml');
        } catch (\Exception $e) {}
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        /**
         * Set page identifier and check it for storeID = 0
         */
        $pageIdentifier = \Plumrocket\Amp\Helper\Data::AMP_HOME_PAGE_KEYWORD;
        $page = $this->pageFactory->create();

        if (!$page->checkIdentifier($pageIdentifier, 0)) {

            /**
             * Prepare data for AMP Home Page and create it
             */
            $pageContent = '{{block class="Magento\Framework\View\Element\Template"'
                . ' template="Plumrocket_Amp::homepage/category/list.phtml"}}';
            $homePage = $this->pageRepository->getById($this->configHelper->getHomePageIdentifier());

            $homePageData = [
                \Magento\Cms\Model\Page::IDENTIFIER => $pageIdentifier,
                \Magento\Cms\Model\Page::TITLE => 'Amp Home Page',
                \Magento\Cms\Model\Page::PAGE_LAYOUT => '1column',
                \Magento\Cms\Model\Page::CONTENT_HEADING => 'Amp Home Page',
                \Magento\Cms\Model\Page::CONTENT => $pageContent,
                \Magento\Cms\Model\Page::SORT_ORDER => 0,
                \Magento\Cms\Model\Page::IS_ACTIVE => true,
                \Magento\Cms\Model\Page::META_TITLE => (string) $homePage->getMetaTitle(),
                \Magento\Cms\Model\Page::META_KEYWORDS => (string) $homePage->getMetaKeywords(),
                \Magento\Cms\Model\Page::META_DESCRIPTION => (string) $homePage->getMetaDescription(),
                'stores' => [\Magento\Store\Model\Store::DEFAULT_STORE_ID],
            ];

            $page->setData($homePageData)->save();
        }

        /**
         * Set block identifier and check it for storeID = 0
         */
        $block = $this->blockFactory->create();
        $blockIdentifier = \Plumrocket\Amp\Helper\Data::AMP_FOOTER_LINKS_KEYWORD;
        $block->setStoreId(0)->load($blockIdentifier);

        if (!$block->getId()) {
            /**
             * Prepare data for AMP footer block and create it
             */
            $blockContent = '<ul>'
                .'<li><a href="{{store url=\'about-us\'}}">About Us</a></li>'
                .'<li><a href="{{store url=\'contact\'}}">Contact Us</a></li>'
                .'<li><a href="{{store url=\'customer-service\'}}">Customer Service</a></li>'
                .'<li><a href="{{store url=\'privacy-policy-cookie-restriction-mode\'}}">Privacy Policy</a></li>'
                .'</ul>';

            $footerBlockData = [
                \Magento\Cms\Model\Block::IDENTIFIER => $blockIdentifier,
                \Magento\Cms\Model\Block::TITLE => 'AMP Footer Links',
                \Magento\Cms\Model\Block::CONTENT => $blockContent,
                \Magento\Cms\Model\Block::IS_ACTIVE => true,
                'page_layout' => '1column',
                'stores' => [0],
            ];

            $block->setData($footerBlockData)->save();
        }
    }
}
