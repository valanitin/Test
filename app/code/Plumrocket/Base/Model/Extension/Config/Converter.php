<?php
/**
 * @package     Plumrocket_Base
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license/  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\Base\Model\Extension\Config;

use Magento\Framework\Config\ConverterInterface;
use Magento\Framework\Data\Argument\Interpreter\Boolean as BooleanInterpreter;
use Plumrocket\Base\Api\Data\ExtensionInformationInterface;

class Converter implements ConverterInterface
{
    /**
     * @var BooleanInterpreter
     */
    private $booleanInterpreter;

    /**
     * @param \Magento\Framework\Data\Argument\Interpreter\Boolean $booleanInterpreter
     */
    public function __construct(BooleanInterpreter $booleanInterpreter)
    {
        $this->booleanInterpreter = $booleanInterpreter;
    }

    /**
     * Convert dom node tree to array
     *
     * @param \DOMDocument $source
     * @return array
     */
    public function convert($source)
    {
        $output = [];
        /** @var \DOMNodeList $extensions */
        $extensions = $source->getElementsByTagName('extension');
        /** @var \DOMNode $extension */
        foreach ($extensions as $extension) {
            $extensionConfig = [
                ExtensionInformationInterface::FIELD_CONFIG_SECTION => '',
                ExtensionInformationInterface::FIELD_IS_ENABLED_PATH => '',
                ExtensionInformationInterface::FIELD_DOCUMENTATION => '',
                ExtensionInformationInterface::FIELD_WIKI => '',
                ExtensionInformationInterface::FIELD_IS_SERVICE => false,
                ExtensionInformationInterface::FIELD_URL => '',
                ExtensionInformationInterface::FIELD_MARKETPLACE_URL => '',
                'customer' => ['key' => ''],
                'metapackage' => ['composer_name' => '', 'directory' => ''],
            ];
            /** @var \DOMAttr $attribute */
            foreach ($extension->attributes as $attribute) {
                $value = $attribute->nodeValue;
                if ($attribute->nodeName === ExtensionInformationInterface::FIELD_IS_SERVICE) {
                    $value = $this->booleanInterpreter->evaluate(['value' => $value]);
                }
                $extensionConfig[$attribute->nodeName] = $value;
            }

            $extensionConfig = $this->addCustomerData($extension, $extensionConfig);

            $output[$extension->attributes->getNamedItem('name')->nodeValue] = $extensionConfig;
        }

        return $output;
    }

    private function addCustomerData(\DOMNode $extension, array $extensionConfig): array
    {
        if ($extension->hasChildNodes()) {
            /** @var \DOMNode $childNode */
            foreach ($extension->childNodes as $childNode) {
                if ($childNode->nodeType === XML_ELEMENT_NODE) {
                    /** @var \DOMAttr $attribute */
                    foreach ($childNode->attributes as $attribute) {
                        $extensionConfig[$childNode->nodeName][$attribute->nodeName] = $attribute->nodeValue;
                    }
                }
            }
        }

        return $extensionConfig;
    }
}
