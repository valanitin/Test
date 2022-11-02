<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_SeoToolKit
 */


declare(strict_types=1);

namespace Amasty\SeoToolKit\Model;

use Amasty\SeoToolKit\Api\Data\RedirectInterface;

class Redirect extends \Magento\Framework\Model\AbstractModel implements RedirectInterface
{
    public function _construct()
    {
        parent::_construct();
        $this->_init(\Amasty\SeoToolKit\Model\ResourceModel\Redirect::class);
    }

    /**
     * @inheritdoc
     */
    public function getRedirectId()
    {
        return $this->_getData(RedirectInterface::REDIRECT_ID);
    }

    /**
     * @inheritdoc
     */
    public function setRedirectId($redirectId)
    {
        $this->setData(RedirectInterface::REDIRECT_ID, $redirectId);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getStatus()
    {
        return $this->_getData(RedirectInterface::STATUS);
    }

    /**
     * @inheritdoc
     */
    public function setStatus($status)
    {
        $this->setData(RedirectInterface::STATUS, $status);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getRequestPath()
    {
        return $this->_getData(RedirectInterface::REQUEST_PATH);
    }

    /**
     * @inheritdoc
     */
    public function setRequestPath($requestPath)
    {
        $this->setData(RedirectInterface::REQUEST_PATH, $requestPath);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getTargetPath()
    {
        return $this->_getData(RedirectInterface::TARGET_PATH);
    }

    /**
     * @inheritdoc
     */
    public function setTargetPath($targetPath)
    {
        $this->setData(RedirectInterface::TARGET_PATH, $targetPath);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getRedirectType()
    {
        return $this->_getData(RedirectInterface::REDIRECT_TYPE);
    }

    /**
     * @inheritdoc
     */
    public function setRedirectType($redirectType)
    {
        $this->setData(RedirectInterface::REDIRECT_TYPE, $redirectType);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getUndefinedPageOnly()
    {
        return $this->_getData(RedirectInterface::UNDEFINED_PAGE_ONLY);
    }

    /**
     * @inheritdoc
     */
    public function setUndefinedPageOnly($undefinedPageOnly)
    {
        $this->setData(RedirectInterface::UNDEFINED_PAGE_ONLY, $undefinedPageOnly);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getPriority()
    {
        return $this->_getData(RedirectInterface::PRIORITY);
    }

    /**
     * @inheritdoc
     */
    public function setPriority($priority)
    {
        $this->setData(RedirectInterface::PRIORITY, $priority);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getDescription()
    {
        return $this->_getData(RedirectInterface::DESCRIPTION);
    }

    /**
     * @inheritdoc
     */
    public function setDescription($description)
    {
        $this->setData(RedirectInterface::DESCRIPTION, $description);

        return $this;
    }
}
