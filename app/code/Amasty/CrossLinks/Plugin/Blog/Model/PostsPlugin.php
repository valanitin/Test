<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_CrossLinks
 */


namespace Amasty\CrossLinks\Plugin\Blog\Model;

use Amasty\Blog\Model\Posts;
use Amasty\CrossLinks\Helper\Data as CrossLinksHelper;
use Amasty\CrossLinks\Model\ReplaceManager;

class PostsPlugin
{
    /**
     * @var \Amasty\CrossLinks\Model\ReplaceManager
     */
    private $replaceManager;

    /**
     * @var CrossLinksHelper
     */
    private $helper;

    public function __construct(
        CrossLinksHelper $helper,
        ReplaceManager $replaceManager
    ) {
        $this->replaceManager = $replaceManager;
        $this->helper = $helper;
    }

    /**
     * @param Posts $subject
     * @param string $result
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetFullContent(Posts $subject, string $result)
    {
        return $this->processContent($result);
    }

    /**
     * @param Posts $subject
     * @param string $result
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetShortContent(Posts $subject, string $result)
    {
        return  $this->processContent($result);
    }

    /**
     * @param string $content
     * @return string
     */
    private function processContent(string $content)
    {
        if ($content && $this->helper->isActiveForBlog()) {
            $content = $this->replaceManager->processBlogPageContent($content);
        }

        return $content;
    }
}
