<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2015 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

namespace Plumrocket\SocialLoginFree\Block;

/**
 * Can receive following params:
 *
 * show_short_buttons - change button to short version without text
 */
class Buttons extends \Magento\Framework\View\Element\Template
{
    /**
     * @var int
     */
    protected $_countFullButtons = 2;

    /**
     * @var bool
     */
    protected $_output2js = false;

    /**
     * @var null
     */
    protected $_checkPosition = null;

    /**
     * @var \Plumrocket\SocialLoginFree\Helper\Data
     */
    protected $dataHelper;

    /**
     * @var \Plumrocket\SocialLoginFree\Api\Buttons\ProviderInterface
     */
    private $buttonsProvider;

    /**
     * @var \Plumrocket\SocialLoginFree\Helper\Config
     */
    private $config;

    /**
     * Buttons constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context          $context
     * @param \Plumrocket\SocialLoginFree\Helper\Data                   $dataHelper
     * @param \Plumrocket\SocialLoginFree\Api\Buttons\ProviderInterface $buttonsProvider
     * @param \Plumrocket\SocialLoginFree\Helper\Config                 $config
     * @param array                                                     $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Plumrocket\SocialLoginFree\Helper\Data $dataHelper,
        \Plumrocket\SocialLoginFree\Api\Buttons\ProviderInterface $buttonsProvider,
        \Plumrocket\SocialLoginFree\Helper\Config $config,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->dataHelper = $dataHelper;
        $this->buttonsProvider = $buttonsProvider;
        $this->config = $config;
    }

    /**
     * @return \Plumrocket\SocialLoginFree\Helper\Data
     */
    public function getHelper()
    {
        return $this->dataHelper;
    }

    /**
     * @return array
     */
    public function getPreparedButtons(): array
    {
        return $this->buttonsProvider->getPreparedButtons(true, false);
    }

    public function hasButtons()
    {
        return (bool) $this->getPreparedButtons();
    }

    /**
     * @return bool
     */
    public function showShortButtons(): bool
    {
        return (bool) $this->getData('show_short_buttons');
    }

    public function showFullButtons(): bool
    {
        return ! $this->showShortButtons();
    }

    public function setOutput2js($flag = true)
    {
        $this->_output2js = (bool)$flag;
    }

    public function checkPosition($position = null)
    {
        $this->_checkPosition = $position;
    }

    public function _afterToHtml($html)
    {
        if ($this->_checkPosition && ! $this->config->isModulePositionEnabled($this->_checkPosition)) {
            $html = '';
        }

        if ($this->_output2js && trim($html)) {
            $html = '<script type="text/javascript">'
                . 'window.psloginButtons = \''
                . str_replace(["\n", 'script'], ['', "scri'+'pt"], $this->escapeJs($html)) . '\';'
                . '</script>';
        }

        return parent::_afterToHtml($html);
    }
}
