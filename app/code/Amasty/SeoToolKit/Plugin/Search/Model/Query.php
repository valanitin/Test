<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_SeoToolKit
 */


declare(strict_types=1);

namespace Amasty\SeoToolKit\Plugin\Search\Model;

use Amasty\SeoToolKit\Controller\Redirect\Index;

class Query
{
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
     * @param \Magento\Search\Model\Query $subject
     * @param $proceed
     * @param $numResults
     * @return \Magento\Search\Model\Query
     */
    public function aroundSaveNumResults($subject, $proceed, $numResults)
    {
        if ($this->request->getParam(Index::AMTOOLKIT_404_REDIRECT) !== null) {
            return $subject;
        }

        return $proceed($numResults);
    }

    /**
     * @param \Magento\Search\Model\Query $subject
     * @param $proceed
     * @return \Magento\Search\Model\Query
     */
    public function aroundSaveIncrementalPopularity($subject, $proceed)
    {
        if ($this->request->getParam(Index::AMTOOLKIT_404_REDIRECT) !== null) {
            return $subject;
        }

        return $proceed();
    }
}
