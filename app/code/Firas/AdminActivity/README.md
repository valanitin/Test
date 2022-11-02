# Magento 2 - Admin Activity by [Firas](https://firas.co.uk/)
- Easily track every admin activity like add, edit, delete, print, view, mass update etc.
- Failed attempts of admin login are recorded as well. You get access to the user’s login information and IP address.
- Track page visit history of admin.
- Track fields that have been changed from the backend.
- Allow administrator to revert the modification.

## **Installation**
1. Composer Installation
      - Navigate to your Magento root folder<br />
            `cd path_to_the_magento_root_directory`
      - Then run the following command<br />
          `composer require firas/module-admin-activity`
      - Make sure that composer finished the installation without errors.

2. Command Line Installation
      - Backup your web directory and database.
      - Download the latest Cron Scheduler installation package firas-admin-activity-vvvv.zip [here](https://github.com/firas/magento2-admin-activity/releases)
      - Navigate to your Magento root folder<br />
          `cd path_to_the_magento_root_directory`<br />
      - Upload contents of the Admin Activity Log installation package to your Magento root directory
      - Then run the following command<br />
          `php bin/magento module:enable Firas_AdminActivity`<br />
   
- After installing the extension, run the following command
```
php bin/magento setup:upgrade
php bin/magento setup:di:compile
php bin/magento setup:static-content:deploy
php bin/magento cache:flush
```
- Log out from the backend and log in again.

Find More details on [Firas](https://firas.co.uk/extensions/magento2-admin-activity)

## Where will it appear in the Admin Panel
### Admin Activity Log
Go to **System > Admin Activity by Firas > Admin Activity**. Here you can See the list of admin activity logs and page visit history.

<img src="https://firas.co.uk/wp-content/uploads/2018/06/admin-activity-history.png"/><br/>

- Page Visit History

<img src="https://firas.co.uk/wp-content/uploads/2018/06/page-visit-history.png"/><br/>

By clicking View in each admin activity log, you can see the slider with admin activity log details.

<img src="https://firas.co.uk/wp-content/uploads/2018/05/activity-log-slider.png"/> <br/>

### Login Activity
Go to **System > Admin Activity by Firas > Login Activity**. Here you can See the list of login activity logs.

<img src="https://firas.co.uk/wp-content/uploads/2018/06/admin-activity-history.png"/><br/>

## Configuration
You need to follow this path. **System > Admin Activity by Firas > Configuration**
- General configuration

<img src="https://firas.co.uk/wp-content/uploads/2018/05/configuration-general-section.png" /> <br/>

- Allow Module Section

<img src="https://firas.co.uk/wp-content/uploads/2018/05/configuration-allow-module-section.png" /> <br/>

## Need Additional Features?
Feel free to get in touch with us at https://firas.co.uk/get-in-touch/

## Other Firas Extensions
* [Magento 2 Cron Scheduler](https://firas.co.uk/extensions/magento2-cron-scheduler/)
* [Magento 2 Login As Customer](https://firas.co.uk/extensions/magento2-login-as-customer/)
* [Magento 2 Inventory Log](https://firas.co.uk/extensions/magento2-inventory-log/)
* [Magento 2 Enhanced SMTP](https://firas.co.uk/extensions/magento2-enhanced-smtp/)
* [Magento 2 Customer Password](https://github.com/firas/magento2-customer-password/)

## Contribution
Well unfortunately there is no formal way to contribute, we would encourage you to feel free and contribute by:
 
  - Creating bug reports, issues or feature requests on [Github](https://github.com/firas/magento2-admin-activity/issues)
  - Submitting pull requests for improvements.
    
We love answering questions or doubts simply ask us in issue section. We're looking forward to hearing from you!
 
  - Follow us [@Firas](https://twitter.com/Firas)
  - <a href="mailto:support@firas.co.uk">Email Us</a>
  - Have a look at our [documentation](https://firas.co.uk/docs/admin-activity/)

