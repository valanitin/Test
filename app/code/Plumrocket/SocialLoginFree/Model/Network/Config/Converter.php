<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2022 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\SocialLoginFree\Model\Network\Config;

use Magento\Framework\Config\ConverterInterface;

/**
 * @since 3.2.0
 */
class Converter implements ConverterInterface
{
    /**
     * Convert dom node tree to array
     *
     * @param \DOMDocument $source
     * @return array
     */
    public function convert($source): array
    {
        $output = [];
        $networks = $source->getElementsByTagName('network');
        /** @var \DOMNode $networkNode */
        foreach ($networks as $networkNode) {
            $networkConfig = [];
            /** @var \DOMAttr $attribute */
            foreach ($networkNode->attributes as $attribute) {
                $networkConfig[$attribute->nodeName] = $attribute->nodeValue;
            }

            /** @var \DOMNode $childNode */
            foreach ($networkNode->childNodes as $childNode) {
                if ($this->notEmpty($childNode)) {
                    /** @var \DOMAttr $childAttribute */
                    foreach ($childNode->attributes as $childAttribute) {
                        if (in_array($childAttribute->nodeName, ['width', 'height'], true)) {
                            $childAttributeValue = (int) $childAttribute->nodeValue;
                        } else {
                            $childAttributeValue = $childAttribute->nodeValue;
                        }

                        $networkConfig[$childNode->nodeName][$childAttribute->nodeName] = $childAttributeValue;
                    }

                    if ($childNode->hasChildNodes()) {
                        $networkConfig[$childNode->nodeName] = $this->convertChildNodes(
                            $childNode,
                            $networkConfig[$childNode->nodeName]
                        );
                    }
                }
            }
            $output[$networkNode->attributes->getNamedItem('code')->nodeValue] = $networkConfig;
        }

        return $this->addDefaultValues($output);
    }

    /**
     * Convert child nodes to array.
     *
     * @param \DOMNode $node
     * @param array    $config
     * @return array
     */
    private function convertChildNodes(\DOMNode $node, array $config): array
    {
        /** @var \DOMNode $childNode */
        foreach ($node->childNodes as $childNode) {
            if ($this->notEmpty($childNode)) {
                switch ($childNode->nodeName) {
                    case 'path':
                        $config[$childNode->nodeName] = $childNode->nodeValue;
                        break;
                    case 'param':
                        $paramName = $childNode->attributes->getNamedItem('name')->nodeValue;
                        $config['params'][$paramName] = $childNode->nodeValue;
                        break;
                    case 'url':
                        /** @var \DOMAttr $attribute */
                        foreach ($childNode->attributes as $attribute) {
                            $config[$childNode->nodeName][$attribute->nodeName] = $attribute->nodeValue;
                        }
                        $config[$childNode->nodeName] = $this->convertChildNodes(
                            $childNode,
                            $config[$childNode->nodeName]
                        );
                        break;
                }
            }
        }

        return $config;
    }

    /**
     * Check if node is not empty.
     *
     * @param \DOMNode $node
     * @return bool
     */
    private function notEmpty(\DOMNode $node): bool
    {
        $nodeType = (int) $node->nodeType;
        if ($nodeType === XML_ELEMENT_NODE) {
            return true;
        }

        if ($nodeType === XML_CDATA_SECTION_NODE || $nodeType === XML_TEXT_NODE) {
            return '' !== trim($node->nodeValue);
        }

        return false;
    }

    /**
     * Add default values to array.
     *
     * We cannot parse defaults values from xml as it set in xsd.
     * Therefor we just duplicate default values here.
     *
     * @param array $output
     * @return array
     */
    private function addDefaultValues(array $output): array
    {
        return array_map(
            static function ($networkConfig) {
                if (! isset($networkConfig['protocol'])) {
                    $networkConfig['protocol'] = 'OAuth';
                }
                return $networkConfig;
            },
            $output
        );
    }
}
