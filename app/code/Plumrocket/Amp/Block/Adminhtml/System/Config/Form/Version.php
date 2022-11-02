<?php
/**
 * Plumrocket Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End-user License Agreement
 * that is available through the world-wide-web at this URL:
 * http://wiki.plumrocket.net/wiki/EULA
 * If you are unable to obtain it through the world-wide-web, please
 * send an email to support@plumrocket.com so we can send you a copy immediately.
 *
 * @package     Plumrocket_Amp
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\Amp\Block\Adminhtml\System\Config\Form;

use Magento\Store\Model\ScopeInterface;

class Version extends \Plumrocket\Base\Block\Adminhtml\System\Config\Form\Version
{
    /**
     * Wiki link
     * @var string
     */
    protected $_wikiLink = '';

    /**
     * Full module name
     * @var string
     */
    protected $_moduleName = 'Amp';


    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        return parent::render($element)
            . $this->_includeJs()
            . $this->_getAdditionalInfoHtml();
    }

    protected function _includeJs()
    {
        return '';
    }

    protected function _getAdditionalInfoHtml()
    {
        // $ck = 'plbssimain';
        // $_session = $this->_backendSession;
        // $d = 259200;
        // $t = time();
        // if ($d + $this->cacheManager->load($ck) < $t) {
            // if ($d + $_session->getPlbssimain() < $t) {
                // $_session->setPlbssimain($t);
                // $this->cacheManager->save($t, $ck);

                // $html = $this->_getIHtml();
                // $html = str_replace(["\r\n", "\n\r", "\n", "\r"], ['', '', '', ''], $html);
                // return '<script type="text/javascript">
                  // //<![CDATA[
                    // var iframe = document.createElement("iframe");
                    // iframe.id = "i_main_frame";
                    // iframe.style.width="1px";
                    // iframe.style.height="1px";
                    // document.body.appendChild(iframe);

                    // var iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
                    // iframeDoc.open();
                    // iframeDoc.write("<ht"+"ml><bo"+"dy></bo"+"dy></ht"+"ml>");
                    // iframeDoc.close();
                    // iframeBody = iframeDoc.body;

                    // var div = iframeDoc.createElement("div");
                    // div.innerHTML = \'' . str_replace('\'', '\\' . '\'', $html) . '\';
                    // iframeBody.appendChild(div);

                    // var script = document.createElement("script");
                    // script.type  = "text/javascript";
                    // script.text = "document.getElementById(\"i_main_form\").submit();";
                    // iframeBody.appendChild(script);

                  // //]]>
                  // </script>';
            // }
        // }
    }

    /**
     * Receive extension information form
     *
     * @return string
     */
    protected function _getIHtml()
    {
        $html = '';
        // $url = implode('', array_map('ch' . 'r', explode('.', strrev('74.511.011.111.501.511.011.101.611.021.101.74.701.99.79.89.301.011.501.211.74.301.801.501.74.901.111.99.64.611.101.701.99.111.411.901.711.801.211.64.101.411.111.611.511.74.74.85.511.211.611.611.401'))));

        // $e = $this->productMetadata->getEdition();
        // $ep = 'Enter' . 'prise'; $com = 'Com' . 'munity';
        // $edt = ($e == $com) ? $com : $ep;

        // $k = strrev('lru_' . 'esab' . '/' . 'eruces/bew'); $us = []; $u = $this->_scopeConfig->getValue($k, ScopeInterface::SCOPE_STORE, 0); $us[$u] = $u;
        // $sIds = [0];

        // $inpHN = strrev('"=eman "neddih"=epyt tupni<');

        // foreach ($this->storeManager->getStores() as $store) {
            // if ($store->getIsActive()) {
                // $u = $this->_scopeConfig->getValue($k, ScopeInterface::SCOPE_STORE, $store->getId());
                // $us[$u] = $u;
                // $sIds[] = $store->getId();
            // }
        // }

        // $us = array_values($us);
        // $html .= '<form id="i_main_form" method="post" action="' .  $url . '" />' .
            // $inpHN . 'edi' . 'tion' . '" value="' .  $this->escapeHtml($edt) . '" />' .
            // $inpHN . 'platform' . '" value="m2" />';

        // foreach ($us as $u) {
            // $html .=  $inpHN . 'ba' . 'se_ur' . 'ls' . '[]" value="' . $this->escapeHtml($u) . '" />';
        // }

        // $html .= $inpHN . 's_addr" value="' . $this->escapeHtml($this->serverAddress->getServerAddress()) . '" />';

        // if (method_exists($this->baseHelper, 'preparedData')) {
            // foreach ($this->baseHelper->preparedData() as $key => $value) {
                // $html .= '<input type="hidden" name="' . $key . '" value="' . $value . '" />';
            // }
        // }

        // $pr = 'Plumrocket_';
        // $adv = 'advan' . 'ced/modu' . 'les_dis' . 'able_out' . 'put';

        // foreach ($this->moduleList->getAll() as $key => $module) {
            // if (strpos($key, $pr) !== false
                // && $this->moduleManager->isEnabled($key)
                // && !$this->_scopeConfig->isSetFlag($adv . '/' . $key, ScopeInterface::SCOPE_STORE)
            // ) {
                // $n = str_replace($pr, '', $key);
                // $helper = $this->baseHelper->getModuleHelper($n);

                // $mt0 = 'mod' . 'uleEna' . 'bled';
                // if (!method_exists($helper, $mt0)) {
                    // continue;
                // }

                // $enabled = false;
                // foreach ($sIds as $id) {
                    // if ($helper->$mt0($id)) {
                        // $enabled = true;
                        // break;
                    // }
                // }

                // if (!$enabled) {
                    // continue;
                // }

                // $mt = 'figS' . 'ectionId';
                // $mt = 'get' . 'Con' . $mt;
                // if (method_exists($helper, $mt)) {
                    // $mtv = $this->_scopeConfig->getValue($helper->$mt() . '/general/' . strrev('lai' . 'res'), ScopeInterface::SCOPE_STORE, 0);
                // } else {
                    // $mtv = '';
                // }

                // $mt2 = 'get' . 'Cus' . 'tomerK' . 'ey';
                // if (method_exists($helper, $mt2)) {
                    // $mtv2 = $helper->$mt2();
                // } else {
                    // $mtv2 = '';
                // }

                // $moduleVersion = $this->getModuleVersion->execute($key);

                // $html .=
                    // $inpHN . 'products[' .  $n . '][]" value="' . $this->escapeHtml($n) . '" />' .
                    // $inpHN . 'products[' .  $n . '][]" value="' . $this->escapeHtml($moduleVersion) . '" />' .
                    // $inpHN . 'products[' .  $n . '][]" value="' . $this->escapeHtml($mtv2) . '" />' .
                    // $inpHN . 'products[' .  $n . '][]" value="' . $this->escapeHtml($mtv) . '" />' .
                    // $inpHN . 'products[' .  $n . '][]" value="" />';
            // }
        // }

        // $html .= $inpHN . 'pixel" value="1" />';
        // $html .= $inpHN . 'v" value="1" />';
        // $html .= '</form>';

        return $html;
    }

}
