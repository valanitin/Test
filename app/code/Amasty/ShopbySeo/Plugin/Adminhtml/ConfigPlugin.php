<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ShopbySeo
 */


namespace Amasty\ShopbySeo\Plugin\Adminhtml;

class ConfigPlugin
{
    const AMASTY_SHOPBY_SEO = 'amasty_shopby_seo';

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    private $messageManager;

    /**
     * @var \Amasty\ShopbySeo\Model\Source\OptionSeparator
     */
    private $optionSeparator;

    /**
     * @var \Magento\Framework\Filter\FilterManager
     */
    private $filter;

    public function __construct(
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Amasty\ShopbySeo\Model\Source\OptionSeparator $optionSeparator,
        \Magento\Framework\Filter\FilterManager $filter
    ) {
        $this->messageManager = $messageManager;
        $this->optionSeparator = $optionSeparator;
        $this->filter = $filter;
    }

    /**
     * @param $subject
     * @return mixed
     */
    public function beforeSave($subject)
    {
        $groups = $subject->getGroups();
        if ($subject->getSection() !== self::AMASTY_SHOPBY_SEO) {
            return $groups;
        }

        $fields = isset($groups['url']) ? $groups['url']['fields'] : [];

        if (!$fields['mode']['value']) {
            return $groups;
        }

        $resultSpecialChar = isset($fields['special_char']['value']) ? $fields['special_char']['value'] : '_';
        $message = '';

        if (isset($groups['url']['fields']['filter_word'])) {
            $groups['url']['fields']['filter_word']['value'] = str_replace(
                '-',
                $resultSpecialChar,
                $this->filter->translitUrl($groups['url']['fields']['filter_word']['value'])
            );
        }
        if ($message) {
            $groups['url']['fields']['special_char']['value'] = $resultSpecialChar;
            $this->messageManager->addWarningMessage($message);
        }
        $subject->setGroups($groups);

        return $groups;
    }
}
