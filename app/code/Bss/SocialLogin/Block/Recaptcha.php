<?php
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * @category   BSS
 * @package    Bss_AjaxSocialLogin
 * @author     Extension Team
 * @copyright  Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\SocialLogin\Block;

use Magento\Framework\View\Element\Template;

/**
 * Class Recaptcha
 *
 * @package Bss\SocialLogin\Block
 */
class Recaptcha extends Template
{
    /**
     * @var array
     */
    protected $_languages = [
        'ar_DZ|ar_SA|ar_KW|ar_MA|ar_EG|az_AZ|' => 'ar',
        'bg_BG' => 'bg',
        'ca_ES' => 'ca',
        'zh_CN' => 'zh-CN',
        'zh_HK|zh_TW' => 'zh-TW',
        'hr_HR' => 'hr',
        'cs_CZ' => 'cs',
        'da_DK' => 'da',
        'nl_NL' => 'nl',
        'en_GB|en_AU|en_NZ|en_IE|cy_GB' => 'en-GB',
        'en_US|en_CA' => 'en',
        'fil_PH' => 'fil',
        'fi_FI' => 'fi',
        'fr_FR' => 'fr',
        'fr_CA' => 'fr-CA',
        'de_DE' => 'de',
        'de_AT)' => 'de-AT',
        'de_CH' => 'de-CH',
        'el_GR' => 'el',
        'he_IL' => 'iw',
        'hi_IN' => 'hi',
        'hu_HU' => 'hu',
        'gu_IN|id_ID' => 'id',
        'it_IT|it_CH' => 'it',
        'ja_JP' => 'ja',
        'ko_KR' => 'ko',
        'lv_LV' => 'lv',
        'lt_LT' => 'lt',
        'nb_NO' => 'no',
        'fa_IR' => 'fa',
        'pl_PL' => 'pl',
        'pt_BR' => 'pt-BR',
        'pt_PT' => 'pt-PT',
        'ro_RO' => 'ro',
        'ru_RU' => 'ru',
        'sr_RS' => 'sr',
        'sk_SK' => 'sk',
        'sl_SI' => 'sl',
        'es_ES|gl_ES' => 'es',
        'es_AR|es_CL|es_CO|es_CR|es_MX|es_PA|es_PE|es_VE' => 'es-419',
        'sv_SE' => 'sv',
        'th_TH' => 'th',
        'tr_TR' => 'tr',
        'uk_UA' => 'uk',
        'vi_VN' => 'vi'
    ];
    /**
     * @var \Bss\SocialLogin\Helper\Data
     */
    protected $helper;
    /**
     * @var \Magento\Framework\Locale\Resolver
     */
    protected $resolverLocale;

    /**
     * Recaptcha constructor.
     * @param Template\Context $context
     * @param \Bss\SocialLogin\Helper\Data $helper
     * @param \Magento\Framework\Locale\Resolver $resolverLocale
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        \Bss\SocialLogin\Helper\Data $helper,
        \Magento\Framework\Locale\Resolver $resolverLocale,
        array $data = []
    ) {
        $this->resolverLocale = $resolverLocale;
        $this->helper = $helper;
        parent::__construct($context, $data);
    }

    /**
     * @return mixed
     */
    public function getHelper()
    {
        return $this->helper;
    }

    /**
     * @return string
     */
    public function getRecaptchaScript()
    {
        if (! $this->getHelper()->moduleEnabled()) {
            return '';
        }
        
        $language = $this->resolverLocale->getLocale();

        $lang = 'en';

        foreach ($this->_languages as $options => $_lang) {
            if (stristr($options, $language)) {
                $lang = $_lang;
            }
        }

        $query = [
            'render' => 'explicit',
            'hl'     => $lang
        ];

        return sprintf(
            '<script src="https://www.google.com/recaptcha/api.js?%s" async defer></script>',
            http_build_query($query)
        );
    }

    /**
     * @return string
     */
    public function getRecaptchaId()
    {
        return uniqid();
    }
}
