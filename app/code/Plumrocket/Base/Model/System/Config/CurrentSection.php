<?php
/**
 * @package     Plumrocket_Base
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\Base\Model\System\Config;

use Magento\Config\Model\Config\Structure;
use Magento\Config\Model\Config\Structure\Element\Section;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Retrieve current section in system config and allow check if it's one of plumrocket sections
 *
 * @since 2.3.1
 */
class CurrentSection
{
    /**
     * @var \Magento\Config\Model\Config\Structure
     */
    private $configStructure;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;

    /**
     * @param \Magento\Config\Model\Config\Structure  $configStructure
     * @param \Magento\Framework\App\RequestInterface $request
     */
    public function __construct(
        Structure $configStructure,
        RequestInterface $request
    ) {
        $this->configStructure = $configStructure;
        $this->request = $request;
    }

    /**
     * Get section id.
     *
     * @return string|null
     */
    public function getId(): ?string
    {
        if ($section = $this->get()) {
            return $section->getId();
        }

        return null;
    }

    /**
     * Get current section.
     *
     * @return \Magento\Config\Model\Config\Structure\Element\Section|null
     */
    public function get(): ?Section
    {
        $current = $this->request->getParam('section', '');

        if (! $current) {
            try {
                $section = $this->configStructure->getFirstSection();
            } catch (LocalizedException $e) {
                $section = null;
            }
        } else {
            $section = $this->configStructure->getElement($current);
            if (! $section instanceof Section) {
                $section = null;
            }
        }

        return $section;
    }

    /**
     * Check if current section is related one of plumrocket extensions.
     *
     * @return bool
     */
    public function isPlumrocketExtension(): bool
    {
        $section = $this->get();
        return $section && 'plumrocket' === $section->getAttribute('tab');
    }
}
