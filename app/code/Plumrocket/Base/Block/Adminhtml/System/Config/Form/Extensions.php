<?php
/**
 * @package     Plumrocket_Base
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license/  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\Base\Block\Adminhtml\System\Config\Form;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Data\Form\Element\Renderer\RendererInterface;
use Magento\Framework\Phrase;
use Plumrocket\Base\ViewModel\GetExtensionInformationWithUpdates;

/**
 * @since 2.3.0
 */
class Extensions extends Template implements RendererInterface
{
    /**
     * @var string
     */
    protected $_template = 'Plumrocket_Base::system/config/extensions.phtml';

    /**
     * @var string
     */
    private $htmlId = '';

    /**
     * @var \Plumrocket\Base\ViewModel\GetExtensionInformationWithUpdates
     */
    private $extensionInformationWithUpdates;

    /**
     * Extensions constructor.
     *
     * @param \Magento\Backend\Block\Template\Context                       $context
     * @param \Plumrocket\Base\ViewModel\GetExtensionInformationWithUpdates $extensionInformationWithUpdates
     * @param array                                                         $data
     */
    public function __construct(
        Context $context,
        GetExtensionInformationWithUpdates $extensionInformationWithUpdates,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->extensionInformationWithUpdates = $extensionInformationWithUpdates;
    }

    /**
     * Render fieldset html
     *
     * @param  \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(AbstractElement $element): string
    {
        $this->htmlId = $element->getId();
        return $this->toHtml();
    }

    /**
     * Get list of extensions.
     *
     * @return array
     */
    public function getExtensions(): array
    {
        return $this->extensionInformationWithUpdates->execute();
    }

    /**
     * Get label.
     *
     * @param array $extension
     * @return string
     */
    public function getLabel(array $extension): string
    {
        return "{$extension['name']} {$extension['installedVersion']}";
    }

    /**
     * Get label for extension with available update.
     *
     * @param array $extension
     * @return \Magento\Framework\Phrase
     */
    public function getNewUpdateLabel(array $extension): Phrase
    {
        $count = count($extension['updates']);
        return $count === 1 ? __('New Update Available') : __('%1 New Updates Available', $count);
    }

    /**
     * Get html id.
     *
     * @return string
     */
    public function getHtmlId(): string
    {
        return $this->htmlId;
    }
}
