# Magento2 Create Admin Grid With CURD Operations
This is an example module for Create Admin Collection Grid , Edit/Add Grid Row And Installer In Magento2
## Manually Installation

Magento2 module installation is very easy, please follow the steps for installation-

=> Download and unzip the respective extension zip and create Firas(vendor) and Grid(module) name folder inside your magento/app/code/ directory and then move all module's files into magento root directory Magento2/app/code/Firas/Grid/ folder.

## Install with Composer as you go
Specify the version of the module you need, and go.
    
    composer config repositories.reponame vcs https://github.com/firasabhi/magento2-create-admin-grid-CURD-operations
    composer require firasabhi/magento2-create-admin-grid-CURD-operations:dev-master
    

## Run following command via terminal from magento root directory 
  
     $ php bin/magento setup:upgrade
     $ php bin/magento setup:di:compile
     $ php bin/magento setup:static-content:deploy

=> Flush the cache and reindex all.

now module is properly installed

## Code explanations 

https://firas.com/blog/create-grid-edit-add-grid-row-and-installer-in-magento2
