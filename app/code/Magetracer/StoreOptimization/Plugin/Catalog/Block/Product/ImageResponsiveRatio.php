<?php
/**
 * Mage Tracer.
 *
 * @category  Magetracer
 * @package   Magetracer_StoreOptimization
 * @author    Magetracer
 * @copyright Copyright (c) Mage Tracer (https://www.magetracer.com)
 * @license   https://www.magetracer.com/license.html
 */
namespace Magetracer\StoreOptimization\Plugin\Catalog\Block\Product;

use Magento\Catalog\Block\Product\Image;

class ImageResponsiveRatio
{

    /**
     * @var Magetracer\StoreOptimization\Helper\Image
     */
    protected $imageConfig;

    /**
     * @var Magetracer\StoreOptimization\Helper\Data
     */
    protected $helper;

    /**
     *
     * @param \Magetracer\StoreOptimization\Helper\Image $imageConfig
     * @param \Magetracer\StoreOptimization\Helper\Data $helper
     */
    public function __construct(
        \Magetracer\StoreOptimization\Helper\Image $imageConfig,
        \Magetracer\StoreOptimization\Helper\Data $helper
    ) {
        $this->imageConfig = $imageConfig;
        $this->helper = $helper;
    }

    /**
     * Adjust srcset if required
     *
     * @param Image $subject
     */
    public function beforeToHtml(Image $subject)
    {
        if ($this->imageConfig->getIsImageSrcSetEnable()) {
        
            $imageUrl = $subject->getData('image_url');
            $originalImageUrl = $subject->getData('original_image_url');
            $pixels = $this->imageConfig->getIsImagePixels();
            $pixelsArray = explode(',', $pixels);
            $glue = (strpos($imageUrl, '?') !== false) ? '&' : '?';
            $srcSet = [];
            foreach ($pixelsArray as $pixel) {
                $ratio = 'dpr=' . $pixel . ' ' . $pixel . 'x';
                $srcSet[] = $imageUrl . $glue . $ratio;
            }

            $subject->setData(
                'custom_attributes',
                sprintf('srcset="%s" onerror="this.src=\'%s\'"', implode(',', $srcSet), $originalImageUrl)
            );

        }
    }
    
    /**
     * overwrite template file
     *
     * @param Image $subject
     * @param string $template
     * @return []
     */
    public function beforeSetTemplate(Image $subject, $template)
    {
        if ($template == "Magento_Catalog::product/image_with_borders.phtml") {
            return ["Magetracer_StoreOptimization::catalog/product/image_with_borders.phtml"];
        } elseif ($template == "Magento_Catalog::product/image.phtml") {
            return ["Magetracer_StoreOptimization::catalog/product/image.phtml"];
        } else {
            return [$template];
        }
    }

    /**
     * @param CatalogImage $subject
     * @param \Closure $proceed
     * @return mixed
     */
    public function aroundToHtml(
        Image $subject,
        \Closure $proceed
    ) {
        if (!$this->helper->getIsLazyLoadingEnable() || $this->isMobile()) {
            return $proceed();
        }
        
        $orgImageUrl = $subject->getImageUrl();
        $subject->setImageUrl($subject->getViewFileUrl("images/loader-2.gif"));

        $customAttributes = trim(
            $subject->getCustomAttributes() . 'wk-data-original'
        );

        $subject->setCustomAttributes($customAttributes);

        $result = $proceed();

        $find = [
            'img class="',
            'wk-data-original'
        ];

        $replace = [
            'img class="wk_lazy new-lazy ',
            sprintf(' data-original="%s"', $orgImageUrl),
        ];

        return str_replace($find, $replace, $result);
    }

    function isMobile() {
        $useragent = $_SERVER['HTTP_USER_AGENT'];
        if(preg_match('/(alcatel|amoi|android|avantgo|blackberry|benq|cell|cricket|docomo|elaine|htc|iemobile|iphone|ipad|ipaq|ipod|j2me|java|midp|mini|mmp|mobi|motorola|nec-|nokia|palm|panasonic|philips|phone|playbook|sagem|sharp|sie-|silk|smartphone|sony|symbian|t-mobile|telus|up\.browser|up\.link|vodafone|wap|webos|wireless|xda|xoom|zte)/i', $_SERVER['HTTP_USER_AGENT'])) {
            return true;
        }
        else {
            return false;
        }
    }
}
