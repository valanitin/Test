<?php
/**
 * @package     Plumrocket_Base
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license/  End-user License Agreement
 */

namespace Plumrocket\Base\Model\System\Config;

use Magento\Framework\Exception\NotFoundException;

/**
 * @since 2.5.0
 */
class GetCurrentExtensionName
{
    /**
     * @var \Plumrocket\Base\Model\System\Config\CurrentSection
     */
    private $currentSection;

    /**
     * @param \Plumrocket\Base\Model\System\Config\CurrentSection $currentSection
     */
    public function __construct(CurrentSection $currentSection)
    {
        $this->currentSection = $currentSection;
    }

    /**
     * Get current extension name.
     *
     * @return string
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute(): string
    {
        if ($section = $this->currentSection->get()) {
            /** @var \Magento\Config\Model\Config\Structure\Element\Group $group */
            foreach ($section->getChildren() as $group) {
                if ($group->getId() !== 'general' && $group->getId() !== 'toolbox') {
                    continue;
                }
                /** @var \Magento\Config\Model\Config\Structure\Element\Field $field */
                foreach ($group->getChildren() as $field) {
                    if ($field->getId() !== 'version') {
                        continue;
                    }

                    if (! $field->getAttribute('pr_extension_name')) {
                        $versionFieldData = $field->getData();
                        // TODO: remove "if" after changing system config in all modules
                        if (isset($versionFieldData['frontend_model'])) {
                            return explode('\\', $versionFieldData['frontend_model'])[1];
                        }
                        throw new NotFoundException(__('Version field does not contain extension name.'));
                    }

                    return $field->getAttribute('pr_extension_name');
                }
            }

            throw new NotFoundException(__('Not found version field'));
        }

        throw new NotFoundException(__('Not found section'));
    }
}
