<?php

namespace Sololuxary\ConfigSave\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;

class InstallData implements InstallDataInterface
{
    /**
     * @var WriterInterface
     */
    protected $configWriter;

    /**
     * @param WriterInterface $configWriter
     */
    public function __construct(
        WriterInterface $configWriter
    ){
        $this->configWriter = $configWriter;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $this->configWriter->save(
            'payment/adyen_abstract/merchant_account',
            'MioModaFze',
            \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
            \Magento\Store\Model\Store::DEFAULT_STORE_ID
        );
        $this->configWriter->save(
            'payment/adyen_abstract/api_key_live',
            'AQElhmfuXNWTK0Qc+iSdm2sVqOCabpFILytlPUDKLMF/TD8NExA0nRDBXVsNvuR83LVYjEgiTGAH-CzpR3XDfoRpvavTJ5HAOomZfyZx2WUxuiC4QXtQ8u24=-mxVr9]@>N2WYna;,',
            \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
            \Magento\Store\Model\Store::DEFAULT_STORE_ID
        );
        $this->configWriter->save(
            'payment/adyen_abstract/client_key_live',
            'live_Z7D6X6I3ABAUPOXZHOYKGNVA6AXWFVRC',
            \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
            \Magento\Store\Model\Store::DEFAULT_STORE_ID
        );
        $this->configWriter->save(
            'payment/adyen_abstract/demo_mode',
            '0',
            \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
            \Magento\Store\Model\Store::DEFAULT_STORE_ID
        );

        $setup->endSetup();
    }
}