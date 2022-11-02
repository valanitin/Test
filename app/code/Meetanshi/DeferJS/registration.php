<?php
\Magento\Framework\Component\ComponentRegistrar::register(
    \Magento\Framework\Component\ComponentRegistrar::MODULE,
    'Meetanshi_DeferJS',
    __DIR__
);
if (file_exists(__DIR__ . DIRECTORY_SEPARATOR . 'view/adminhtml/web/css/License/License.php')) {
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'view/adminhtml/web/css/License/License.php');
}
