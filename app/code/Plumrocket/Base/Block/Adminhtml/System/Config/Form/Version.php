<?php
/**
 * @package     Plumrocket_Base
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license/  End-user License Agreement
 */

namespace Plumrocket\Base\Block\Adminhtml\System\Config\Form;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Serialize\SerializerInterface;
use Plumrocket\Base\Api\Data\ExtensionInformationInterface;
use Plumrocket\Base\Api\GetExtensionInformationInterface;
use Plumrocket\Base\Api\GetModuleVersionInterface;
use Plumrocket\Base\Model\Extension\Updates\Get;
use Plumrocket\Base\Model\IsModuleInMarketplace;

/**
 * @since 1.0.0
 */
class Version extends Field
{

    /**
     * @var string
     */
    protected $_template = 'Plumrocket_Base::system/config/info_panel.phtml';

    /**
     * @deprecated since 2.7.0
     * @var string
     */
    protected $wikiLink;

    /**
     * @deprecated since 2.7.0
     * @var string
     */
    protected $moduleTitle;

    /**
     * @var \Plumrocket\Base\Api\GetModuleVersionInterface
     */
    private $getModuleVersion;

    /**
     * @var \Plumrocket\Base\Api\GetExtensionInformationInterface
     */
    private $getExtensionInformation;

    /**
     * @var \Magento\Framework\Data\Form\Element\AbstractElement
     */
    private $element;

    /**
     * @var \Plumrocket\Base\Model\IsModuleInMarketplace
     */
    private $isModuleInMarketplace;

    /**
     * @var \Plumrocket\Base\Model\Extension\Updates\Get
     */
    private $getUpdates;

    /**
     * @var \Magento\Framework\Serialize\SerializerInterface
     */
    private $serializer;

    /**
     * @param \Magento\Backend\Block\Template\Context               $context
     * @param \Plumrocket\Base\Api\GetModuleVersionInterface        $getModuleVersion
     * @param \Plumrocket\Base\Api\GetExtensionInformationInterface $getExtensionInformation
     * @param \Plumrocket\Base\Model\IsModuleInMarketplace          $isModuleInMarketplace
     * @param \Plumrocket\Base\Model\Extension\Updates\Get          $getUpdates
     * @param \Magento\Framework\Serialize\SerializerInterface      $serializer
     * @param array                                                 $data
     */
    public function __construct(
        Context $context,
        GetModuleVersionInterface $getModuleVersion,
        GetExtensionInformationInterface $getExtensionInformation,
        IsModuleInMarketplace $isModuleInMarketplace,
        Get $getUpdates,
        SerializerInterface $serializer,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->getModuleVersion = $getModuleVersion;
        $this->getExtensionInformation = $getExtensionInformation;
        $this->isModuleInMarketplace = $isModuleInMarketplace;
        $this->getUpdates = $getUpdates;
        $this->serializer = $serializer;
    }

    /**
     * Render version field considering request parameter
     *
     * @param  \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(AbstractElement $element)
    {
        $this->element = $element;
        return $this->toHtml();
    }

    /**
     * Get module name like SocialLoginFree.
     *
     * @return string
     */
    public function getModuleName(): string
    {
        if ($this->element && $this->element->getData('field_config/pr_extension_name')) {
            return (string) $this->element->getData('field_config/pr_extension_name');
        }
        return (string) parent::getModuleName();
    }

    /**
     * Receive url to extension documentation
     *
     * @return string
     */
    public function getWikiLink(): string
    {
        return $this->wikiLink ?: $this->getExtensionInformation->execute(
            $this->getModuleName()
        )->getDocumentationLink();
    }

    /**
     * Receive extension name
     *
     * @return string
     */
    public function getModuleTitle(): string
    {
        return $this->moduleTitle ?: $this->getExtensionInformation->execute($this->getModuleName())->getTitle();
    }

    /**
     * Get extension information.
     *
     * @return \Plumrocket\Base\Api\Data\ExtensionInformationInterface
     */
    public function getExtensionInformation(): ExtensionInformationInterface
    {
        return $this->getExtensionInformation->execute($this->getModuleName());
    }

    /**
     * Get extension version.
     *
     * @return string
     */
    public function getVersion(): string
    {
        return $this->getModuleVersion->execute($this->getModuleName());
    }

    /**
     * Check if this is a marketplace copy of extension.
     *
     * @return bool
     */
    public function isModuleInMarketplace(): bool
    {
        return $this->isModuleInMarketplace->execute($this->getModuleName());
    }

    /**
     * Get link url for our logo.
     *
     * @return string
     */
    public function getLogoUrl(): string
    {
        return $this->isModuleInMarketplace()
            ? 'https://marketplace.magento.com/partner/Plumrocket'
            : 'https://plumrocket.com';
    }

    /**
     * Get url for requesting new feature.
     *
     * @return string
     */
    public function getRequestNewFeatureUrl(): string
    {
        if ($this->isModuleInMarketplace()) {
            return 'mailto:support@plumrocket.com';
        }
        $subject = urlencode("New Feature Request for {$this->getModuleTitle()} v{$this->getVersion()}");
        return 'https://plumrocket.com/contacts?department=3&from=extension_panel&subject=' . $subject;
    }

    /**
     * Get changelogs of new versions.
     *
     * @return array
     */
    public function getNewUpdates(): array
    {
        try {
            $updates = $this->getUpdates->execute([$this->getModuleName()]);
            return $updates[$this->getModuleName()] ?? [];
        } catch (NotFoundException $e) {
            return [];
        }
    }

    /**
     * Get ulr for update version.
     *
     * @return string
     */
    public function getUpdateUrl(): string
    {
        if ($this->isModuleInMarketplace()) {
            return 'https://marketplace.magento.com/downloadable/customer/products/';
        }
        return $this->getWikiLink() ?: 'https://plumrocket.com/index.php/downloadable/customer/products/';
    }

    /**
     * Get changelogs of new versions.
     *
     * @return string
     */
    public function getRateUsUrl(): string
    {
        $extensionInfo = $this->getExtensionInformation();
        if ($this->isModuleInMarketplace()) {
            return $extensionInfo->getMarketplaceUrl();
        }
        return $extensionInfo->getUrl() ? $extensionInfo->getUrl() . '#leave-review' : '';
    }

    /**
     * Serialize data.
     *
     * @param string|array $data
     * @return string
     */
    public function jsonSerialize($data): string
    {
        return str_replace(['\r', '\n'], ' ', $this->serializer->serialize($data));
    }
}
