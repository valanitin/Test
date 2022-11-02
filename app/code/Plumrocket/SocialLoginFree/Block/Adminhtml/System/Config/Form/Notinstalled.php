<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2015 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

namespace Plumrocket\SocialLoginFree\Block\Adminhtml\System\Config\Form;

use Magento\Backend\Block\Template\Context;
use Plumrocket\Base\Model\IsModuleInMarketplace;

class Notinstalled extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * @var array|IsModuleInMarketplace
     */
    private $isModuleInMarketplace;

    /**
     * @param \Magento\Backend\Block\Template\Context      $context
     * @param \Plumrocket\Base\Model\IsModuleInMarketplace $isModuleInMarketplace
     * @param array                                        $data
     */
    public function __construct(
        Context $context,
        IsModuleInMarketplace $isModuleInMarketplace,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->isModuleInMarketplace = $isModuleInMarketplace;
    }

    /**
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     *
     * @return string
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $name = 'Twitter & Facebook Login';
        $url = $this->isModuleInMarketplace->execute('Plumrocket_SocialLoginFree')
            ? 'https://marketplace.magento.com/plumrocket-module-psloginpro.html'
            : 'https://store.plumrocket.com/magento-2-extensions/social-login-pro-magento2-extension.html';

        return $this->wrapMessage(
            __(
                'The free version of "%1" extension does not include this network. Please ' .
                '<a href="%2" target="_blank">upgrade to Social Login Pro magento extension</a> ' .
                'in order to receive 50+ social login networks.',
                $name,
                $url
            )
        );
    }

    /**
     * @param $message
     * @return string
     */
    private function wrapMessage($message): string
    {
        return '<div class="psloginfree-notinstalled" ' .
            'style="padding:10px;background-color:#fff;border:1px solid #ddd;margin-bottom:7px;"' .
            '>'.
            $message
            . '</div>';
    }
}
