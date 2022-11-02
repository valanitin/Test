<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */

namespace Amasty\Shopby\Block\Navigation\State;

use Magento\Framework\View\Element\Template;

class Swatch extends \Magento\Framework\View\Element\Template
{
    /**
     * @var  \Amasty\Shopby\Model\Layer\Filter\Item
     */
    protected $filter;

    /**
     * @var \Magento\Swatches\Helper\Data
     */
    protected $swatchHelper;

    /**
     * @var \Magento\Swatches\Helper\Media
     */
    protected $mediaHelper;

    /**
     * @var \Amasty\Shopby\Helper\Group
     */
    protected $groupHelper;

    /**
     * @var \Amasty\Shopby\Helper\Data
     */
    private $amshopbyHelper;

    /**
     * @var bool
     */
    private $showLabels;

    /**
     * @var \Amasty\Shopby\Helper\FilterSetting
     */
    private $filterSettingHelper;

    /**
     * @var array
     */
    private $groupsByCode = [];

    public function __construct(
        Template\Context $context,
        \Magento\Swatches\Helper\Data $swatchHelper,
        \Magento\Swatches\Helper\Media $mediaHelper,
        \Amasty\Shopby\Helper\Group $groupHelper,
        \Amasty\Shopby\Helper\Data $amshopbyHelper,
        \Amasty\Shopby\Helper\FilterSetting $filterSettingHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->swatchHelper = $swatchHelper;
        $this->mediaHelper = $mediaHelper;
        $this->groupHelper = $groupHelper;
        $this->amshopbyHelper = $amshopbyHelper;
        $this->filterSettingHelper = $filterSettingHelper;
    }

    /**
     * @param \Amasty\Shopby\Model\Layer\Filter\Item $filter
     * @return $this
     */
    public function setFilter(\Amasty\Shopby\Model\Layer\Filter\Item $filter)
    {
        $this->filter = $filter;
        return $this;
    }

    /**
     * @param $showLabels
     * @return $this
     */
    public function showLabels($showLabels)
    {
        $this->showLabels = $showLabels;
        return $this;
    }

    /**
     * Get relevant path to template
     *
     * @return string
     */
    public function getTemplate()
    {
        $template = 'layer/filter/swatch/default.phtml';

        return $template;
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getSwatchData()
    {
        $filterAppliedValues = $this->filter->getValue();
        if (!is_array($filterAppliedValues)) {
            $filterAppliedValues = [$filterAppliedValues];
        }

        $attributeOptions = [];
        $eavAttribute = $this->filter->getFilter()->getAttributeModel();
        $groups = $this->groupHelper->getGroupsByAttributeId($eavAttribute->getAttributeId());

        $groupsByCode = [];
        foreach ($groups as $group) {
            $groupsByCode[$group->getGroupCode()] = $group;
        }

        foreach ($filterAppliedValues as $value) {
            $label = '';
            if (isset($groupsByCode[$value])) {
                $group = $groupsByCode[$value];
                $label = $group->getName();
            } else {
                foreach ($eavAttribute->getOptions() as $option) {
                    if ($option->getValue() === $value) {
                        $label = $option->getLabel();
                        break;
                    }
                }
            }

            $attributeOptions[$value] = [
                'link' => '#',
                'custom_style' => '',
                'label' => $this->groupHelper->chooseGroupLabel($label)
            ];
        }

        $swatches = [];
        if (!empty($groups)) {
            foreach ($filterAppliedValues as $key => $value) {
                if (isset($groupsByCode[$value])) {
                    $group = $groupsByCode[$value];
                    unset($filterAppliedValues[$key]);
                    $swatches[$group->getGroupCode()] = [
                        "option_id" => $group->getId(),
                        "type" => $group->getType(),
                        "value" => $group->getVisual()
                    ];
                }
            }
        }

        $swatches += $this->amshopbyHelper->getSwatchesFromImages($filterAppliedValues, $eavAttribute);

        /* not lost keys */
        $swatches = $swatches + $this->swatchHelper->getSwatchesByOptionsId($filterAppliedValues);
        foreach ($attributeOptions as $key => $attributeOption) {
            if ($this->filter->getOptionLabel() == $attributeOption['label']) {
                $swatchId = $key;
                break;
            }
        }

        $resultSwatches = isset($swatches[$swatchId]) ? [$swatches[$swatchId]] : [];
        $data = [
            'attribute_id' => $eavAttribute->getId(),
            'attribute_code' => $eavAttribute->getAttributeCode(),
            'attribute_label' => $eavAttribute->getStoreLabel(),
            'options' => [$attributeOptions[$swatchId]],
            'swatches' => $resultSwatches
        ];

        return $data;
    }

    /**
     * @param $attributeCode
     * @return int|null
     */
    public function getDisplayModeByAttributeCode($attributeCode)
    {
        return $this->filterSettingHelper->getSettingByAttributeCode($attributeCode)->getDisplayMode();
    }

    public function getFilterSetting()
    {
        return null;
    }

    /**
     * @param string $type
     * @param string $filename
     * @return string
     */
    public function getSwatchPath($type, $filename)
    {
        $imagePath = $this->mediaHelper->getSwatchAttributeImage($type, $filename);

        return $imagePath;
    }
}
