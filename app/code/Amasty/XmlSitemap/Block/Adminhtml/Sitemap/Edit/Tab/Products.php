<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_XmlSitemap
 */


namespace Amasty\XmlSitemap\Block\Adminhtml\Sitemap\Edit\Tab;

use Amasty\XmlSitemap\Helper\Data;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Config\Model\Config\Source\YesnoFactory;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;
use Magento\Backend\Block\Widget\Form\Element\Dependence;
use Amasty\XmlSitemap\Model\Source\ProductType;

class Products extends Generic implements TabInterface
{
    /**
     * @var YesnoFactory $yesnoFactory
     */
    protected $yesnoFactory;

    /**
     * @var Data $helper
     */
    protected $helper;

    /**
     * @var ProductType
     */
    private $productType;

    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        YesnoFactory $yesnoFactory,
        Data $helper,
        ProductType $productType,
        array $data = []
    ) {
        parent::__construct($context, $registry, $formFactory, $data);
        $this->yesnoFactory = $yesnoFactory;
        $this->helper = $helper;
        $this->productType = $productType;
    }

    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('Products');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('Products');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }

    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('amxmlsitemap_profile');

        $yesNo = $this->yesnoFactory->create()->toOptionArray();

        $form = $this->_formFactory->create();

        $fieldset = $form->addFieldset('amxmlsitemap_form_products', ['legend' => __('Products')]);

        $fieldset->addField('products', 'select', [
            'label' => __('Include products'),
            'name' => 'products',
            'title' => __('Include products'),
            'values' => $yesNo
        ]);

        $fieldset->addField('hreflang_product', 'select', [
            'label' => __('Add Hreflang Tags'),
            'title' => __('Add Hreflang Tags'),
            'name' => 'hreflang_product',
            'values' => $yesNo
        ]);

        $fieldset->addField('products_thumbs', 'select', [
            'label' => __('Add Images'),
            'name' => 'products_thumbs',
            'title' => __('Add Images'),
            'values' => $yesNo
        ]);

        $fieldset->addField('products_captions', 'select', [
            'label' => __('Add Images Titles'),
            'name' => 'products_captions',
            'title' => __('Add Images Titles'),
            'values' => $yesNo
        ]);

        $fieldset->addField('products_captions_template', 'text', [
            'label' => __('Template for image title'),
            'name' => 'products_captions_template',
            'title' => __('Template for image title'),
            'note' => __('Specify text to be used for empty captions with {product_name} placeholder for product name. 
            Example - "enjoy {product_name} from e-store"')
        ]);

        $fieldset->addField('products_priority', 'text', [
                'label' => __('Priority'),
                'name' => 'products_priority',
                'note' => __('0.01-0.99'),
                'class' => 'validate-number validate-number-range number-range-0.01-0.99'
        ]);

        $fieldset->addField('products_frequency', 'select', [
            'label' => __('Frequency'),
            'name' => 'products_frequency',
            'title' => __('Frequency'),
            'values' => $this->helper->getFrequency()
        ]);

        $fieldset->addField('products_modified', 'select', [
            'label' => __('Include Last Modified'),
            'name' => 'products_modified',
            'title' => __('Include Last Modified'),
            'values' => $yesNo
        ]);

        $fieldset->addField('exclude_out_of_stock', 'select', [
            'label' => __('Exclude Out Of Stock Products'),
            'name' => 'exclude_out_of_stock',
            'title' => __('Exclude Out Of Stock Products'),
            'values' => $yesNo
        ]);

        $fieldset->addField('exclude_product_type', 'multiselect', [
            'label' => __('Exclude Product Types'),
            'name' => 'exclude_product_type',
            'title' => __('Exclude Product Types'),
            'values' => $this->productType->toOptionArray()
        ]);

        $form->addValues($model->getData());

        $this->setChild('form_after', $this->getLayout()
            ->createBlock(Dependence::class)
            ->addFieldMap('products', 'products')
            ->addFieldMap('products_thumbs', 'products_thumbs')
            ->addFieldMap('hreflang_product', 'hreflang_product')
            ->addFieldMap('products_captions', 'products_captions')
            ->addFieldMap('products_captions_template', 'products_captions_template')
            ->addFieldMap('products_priority', 'products_priority')
            ->addFieldMap('products_frequency', 'products_frequency')
            ->addFieldMap('products_modified', 'products_modified')
            ->addFieldMap('exclude_out_of_stock', 'exclude_out_of_stock')
            ->addFieldMap('exclude_product_type', 'exclude_product_type')
            ->addFieldDependence('products_captions_template', 'products_thumbs', 1)
            ->addFieldDependence('products_captions_template', 'products_captions', 1)
            ->addFieldDependence('products_thumbs', 'products', 1)
            ->addFieldDependence('hreflang_product', 'products', 1)
            ->addFieldDependence('products_captions', 'products', 1)
            ->addFieldDependence('products_captions_template', 'products', 1)
            ->addFieldDependence('products_priority', 'products', 1)
            ->addFieldDependence('products_frequency', 'products', 1)
            ->addFieldDependence('products_modified', 'products', 1)
            ->addFieldDependence('exclude_out_of_stock', 'products', 1)
            ->addFieldDependence('exclude_product_type', 'products', 1)
            ->addFieldDependence('products_captions', 'products_thumbs', 1));

        $form->addValues($model->getData());

        $this->setForm($form);

        return parent::_prepareForm();
    }
}
