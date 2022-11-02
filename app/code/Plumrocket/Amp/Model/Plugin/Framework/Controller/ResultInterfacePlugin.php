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
 * @copyright   Copyright (c) 2019 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Amp\Model\Plugin\Framework\Controller;

use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\Page as PageResult;

/**
 * Plugin for changing result before caching
 */
class ResultInterfacePlugin
{
    /**
     * @var \Plumrocket\Amp\Helper\Data
     */
    private $dataHelper;

    /**
     * ResultInterfacePlugin constructor.
     *
     * @param \Plumrocket\Amp\Helper\Data $dataHelper
     */
    public function __construct(
        \Plumrocket\Amp\Helper\Data $dataHelper
    ) {
        $this->dataHelper = $dataHelper;
    }

    /**
     * @param ResultInterface   $subject
     * @param ResponseInterface $response
     * @return null
     */
    public function beforeRenderResult(
        ResultInterface $subject,
        ResponseInterface $response
    ) {
        if ($subject instanceof PageResult && $this->dataHelper->isAmpRequest()) {
            /**
             * Get layout
             */
            $layout = $subject->getLayout();

            /** @var \Plumrocket\Amp\Block\Page\Head\Js $ampJsBlock */
            if ($layout && $ampJsBlock = $layout->getBlock('ampjs')) {
                $output = $layout->getOutput();

                /**
                 * Removing unnecessary elements
                 */
                foreach ($ampJsBlock->getAmpScripts() as $elementName) {
                    if ($elementName === 'amp-form' || $elementName === 'amp-bind') {
                        continue;
                    }

                    if ($elementName === 'amp-youtube' && strpos($output, 'youtube.com/embed') !== false) {
                        continue;
                    }

                    if ($elementName === 'amp-iframe'
                        && (strpos($output, '<amp-iframe') !== false || strpos($output, '<iframe') !== false)
                    ) {
                        continue;
                    }

                    if ($elementName === 'amp-vimeo' && strpos($output, 'player.vimeo.com/video') !== false) {
                        continue;
                    }

                    if (strpos($output, $elementName) === false) {
                        $ampJsBlock->removeJs($elementName);
                    }
                }

                /**
                 * Removing elements of amp-form
                 */
                if (strpos($output, '<form') === false) {
                    $ampJsBlock->removeJs('amp-form');
                }

                /**
                 * Removing elements of amp-bind
                 */
                if (strpos($output, 'AMP.setState') === false) {
                    $ampJsBlock->removeJs('amp-bind');
                }
            }
        }

        return null;
    }

    /**
     * @param ResultInterface $subject
     * @param ResultInterface $result
     * @param ResponseInterface $response
     * @return ResultInterface
     */
    public function afterRenderResult(
        ResultInterface $subject,
        ResultInterface $result,
        ResponseInterface $response
    ) {
        if ($this->dataHelper->isAmpRequest()) {
            /* remove bad html */
            $html = $response->getBody();
            $html = $this->_replaceHtml($html);
            $response->setBody($html);
        }

        return $result;
    }

    /**
     * Replaced disallowed code on page
     * @param  string $html
     * @return string
     */
    protected function _replaceHtml($html)
    {
        $html = str_ireplace(
            ['<video','/video>','<audio','/audio>','<ui','/ui>'],
            ['<amp-video','/amp-video>','<amp-audio','/amp-audio>','<ul','/ul>'],
            $html
        );

        $html = preg_replace(
            '/<iframe[^>]*?youtube\.com\/embed\/([^>|"|\'|?]+)[^>]*?>\s*?<\/iframe\s*?>/i',
            '<amp-youtube data-videoid="$1" layout="responsive" width="480" height="270"></amp-youtube>',
            $html
        );

        $html = preg_replace(
            '/<iframe.+?player\.vimeo\.com\/video\/(.*?)(?:\?|").+?<\/iframe>/s',
            '<amp-vimeo data-videoid="$1" layout="responsive" width="480" height="270"></amp-vimeo>',
            $html
        );

        $html = preg_replace(
            '/\s+(?:style|align|hspace|vspace|itemprop|itemscope|itemtype|dataurl|on\w{4,12}|border|vocab|typeof|container|usemap|cellpadding|cellspacing|nowrap)\s*=\s*(?:"[^"]*"|\'[^\']*\')/i',
            '',
            $html
        ); // do not remove "content", "id", "property", "title"

        //Remove collspan and rowspan from all tags, except <td>
        $html = preg_replace(
            [
                '#(\<(?!td\s|th\s|\/td\s|\/th\s))([^<]*)(?:rowspan="\d+")(.*>)#isU',
                '#(\<(?!td\s|th\s|\/td\s|\/th\s))([^<]*)(?:colspan="\d+")(.*>)#isU',
            ],
            '$1$2 $3',
            $html
        );

        $html = preg_replace(
            '/\s+target(?:\s*=\s*"\s*"|\s*=\s*\'\s*\'|\s*(?!=))/i',
            '',
            $html
        );

        $html = preg_replace(
            '/(<span[^>]+)(content|property)=(?:"[^"]*"|\'[^\']*\')/',
            '$1',
            $html
        );

        /* remove xml attribute from pre tag */
        $html = preg_replace(
            '/(<pre[^>]+)(xml)=(?:"[^"]*"|\'[^\']*\')/',
            '$1',
            $html
        );

        $html = preg_replace('/<font.*?>(.*?)<\/font>/', '$1', $html);
        $html = preg_replace('/<link[^>]+"http:\/\/purl.org[^"]*"[^\/]*\/>/', '', $html);

        $html = str_replace(
            '<link  href="In stock">',
            '',
            $html
        );

        $html = preg_replace('#"(javascript:\s*[^"]*)"#isU', '#nohref', $html);

        $html = preg_replace(
            [
                '#<script((?!ampproject|application\/ld\+json|application\/json).)*>.*</script>#isU',
                '#<style(?:(?!amp-).)*?>[^<]*?<\/style\s*?>#isU',
                //'#<form.*>.*<\/form>#isU', //need to be for search
                '#<map.*>.*<\/map>#isU',
                '#<link\s+href="https?:\/\/schema\.org\/[a-zA-Z0-9_\-\/\?\&]*"\s?\/?>#isU',
                '#(?:<col\s+[^>]*(width=(?:"[^"]*"|\'[^\']*\'))[^>]*>)#isU'
            ],
            '',
            $html
        );

        $html = preg_replace(
            array(
                '#(<a\s+[^>]*)(alt=(?:"[^"]*"|\'[^\']*\'))([^>]*>)#isU',
                '#(<a\s+[^>]*)(type=(?:"[^"]*"|\'[^\']*\'))([^>]*>)#isU',
                '#(<a\s+[^>]*)(property=(?:"[^"]*"|\'[^\']*\'))([^>]*>)#isU',
                '#(<a\s+[^>]*)(width=(?:"[^"]*"|\'[^\']*\'))([^>]*>)#isU',
                '#(<a\s+[^>]*)(height=(?:"[^"]*"|\'[^\']*\'))([^>]*>)#isU',
                '#(<script\s+[^>]*)(defer\s+)([^>]*>)#isU',
            ),
            '$1$3', $html);

        /* Replace width value with data-width-amp value */
        $html = preg_replace('#(<img\s+[^>]*)(?:width=(?:"\w+"|\'\w+\'))([^>]*)(?:data-width-amp="(\w+)")([^>]*>)#isU', '$1 width="$3" $2 $4', $html);
        $html = preg_replace('#(<img\s+[^>]*)(?:height=(?:"\w+"|\'\w+\'))([^>]*)(?:data-height-amp="(\w+)")([^>]*>)#isU', '$1 height="$3" $2 $4', $html);

        /* replace data-width-amp with width */
        $html = preg_replace('#(<img\s+[^>]*)(?:data-width-amp="(\w+)")([^>]*\/?>)#isU', '$1 width="$2" $3', $html);
        $html = preg_replace('#(<img\s+[^>]*)(?:data-height-amp="(\w+)")([^>]*\/?>)#isU', '$1 height="$2" $3', $html);

        /* Add height & width if not exists */
        $html = preg_replace('#(?:<img\s+)((?:(?!height=(?:"\w+"|\'\w+\')).)*)(?:\/>|>)#isU', '<img height="100" $1/>', $html);
        $html = preg_replace('#(?:<img\s+)((?:(?!width=(?:"\w+"|\'\w+\')).)*)(?:\/>|>)#isU', '<img width="290" $1/>', $html);

        $html = preg_replace('#<img\s+([^>]*)(?:data-src="([^"]*)")([^>]*)\/?>#isU', '<img src="$2" $1 $3/>', $html);

        /* Replace img to amp-img */
        $html = preg_replace('#(?:<img\s+)(.*?)(?:\/>|>)#is', '<amp-img $1></amp-img>', $html);

        /* Replace iframe to amp-iframe */
        $additionalAttributes = 'layout="responsive" sandbox="allow-scripts allow-same-origin allow-popups allow-popups-to-escape-sandbox"';
        $placeholder = '<div class="amp-iframe-placeholder" placeholder><span>Loading</span></div>';

        $subPattern = '((?:src|width|height)=["\'](?:[^"\']*)["\'])';
        $pattern = '#<iframe[^>]*'
            . $subPattern
            . '.*'
            . $subPattern
            . '.*'
            . $subPattern
            . '.*>*<\/iframe>#isU';

        $replacement = '<amp-iframe $1 $2 $3 '
            . $additionalAttributes
            . '>'
            . $placeholder
            . '</amp-iframe>';

        $html = preg_replace($pattern, $replacement, $html);

        return $html;
    }
}
