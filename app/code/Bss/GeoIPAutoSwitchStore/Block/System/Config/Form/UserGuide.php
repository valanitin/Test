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
 * @package    Bss_GeoIPAutoSwitchStore
 * @author     Extension Team
 * @copyright  Copyright (c) 2016-2017 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */

namespace Bss\GeoIPAutoSwitchStore\Block\System\Config\Form;

use Magento\Framework\Data\Form\Element\AbstractElement;

class UserGuide extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * @param AbstractElement $element
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        // @codingStandardsIgnoreStart
        $html = '
            <h2>To configure GeoIP work with Varnish Cache:</h2>
            
            1. Log in to the Magento Admin as an administrator.<br/>
            2. Click STORES > Settings > Configuration > ADVANCED > System > Full Page Cache.<br/>
            3. From the Caching Application list, click Varnish Caching.<br/>
            4. Enter a value in the TTL for public content field.<br/>
            5. Export a Varnish configuration file.<br/>
            6. Edit Varnish Configuration file:
            <br/><br/>
            + At <b>sub vcl_recv</b>, find the following code snippet<br/>
            <code>
            # collect all cookies<br/>
            std.collect(req.http.Cookie);
            </code>
            <br/>
            Then, under the founded code, add this code: 
            <br/>
            <textarea>
            if (req.http.Cookie !~ "country_code" && req.url !~ "^/(pub/)?(media|static)/.*\.(ico|css|js|jpg|jpeg|png|gif|tiff|bmp|mp3|ogg|svg|swf|woff|woff2|eot|ttf|otf)$") {
                return (pass);
            }
            </textarea>
            <br/><br/>
            + At <b>sub vcl_hash</b>, find the following code snippet<br/>
            <code>
            if (req.http.cookie ~ "X-Magento-Vary=") {<br/>
                hash_data(regsub(req.http.cookie, "^.*?X-Magento-Vary=([^;]+);*.*$", "\1"));<br/>
            }
            </code>
            <br/>
            Then, under the founded code, add this code: 
            <br/>
            <textarea>
            if (req.http.cookie ~ "country_code=") {
                hash_data(regsub(req.http.Cookie, "^.*?country_code=([^;]*);*.*$", "\1"));
            }
            </textarea><style></style>
        ';
        // @codingStandardsIgnoreEnd
        return $html;
    }
}
