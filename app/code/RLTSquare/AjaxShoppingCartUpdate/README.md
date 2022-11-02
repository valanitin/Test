# AjaxShoppingCartUpdate-Magento2

# Overview

AJAX Shopping Cart Update is built to update the quantity of your customer’s order seamlessly. You can see total price adjustments on the shopping cart page instantly. There are no needless page reloads. There are buttons to increase or decrease the quantity of items on your cart. It is a much simpler approach for updating the products on your cart. Customers don’t have to manually enter the quantity in the cart, if they don’t want to. They don’t have to click ‘Update to Cart’ button every time they add, remove or delete products. This makes the whole process intuitive and flawless. It also helps in decreasing the cart abandonment rate. The fact that customers feel the checkout process is easier, intuitive and faster builds trust in your customers which results in increase in ROI.  
The AJAX Shopping Cart extension is guaranteed to improve effective engagement with your customers. It makes it more appealing and user friendly which greatly enhances the customer experience.

Here are some of the salient features for the extension:

```
1. AJAX-based technology to let customers update quantity of the products to the cart
2. Automatically updates the price without having to reload the page
3. Buttons to increase and decrease the quantity of items
4. AJAX call to cart, summary and mini-cart to automatically update the price
5. Updates the cart in real-time. 
6. User-friendly and seamless way to update cart
```

## Installation

### Magento® Marketplace

This extension will also be available on the Magento® Marketplace when approved.

### Manually

1. Go to Magento® 2 root folder

2. Require/Download this extension:

   Enter following commands to install extension.

   ```
   composer require rltsquare/ajax-shopping-cart-update
   ```

   Wait while composer is updated.
   
   #### OR
   
   You can also download code from this repo under Magento® 2 following directory:
    
    ```
    app/code/RLTSquare/AjaxShoppingCartUpdate
    ```    

3. Enter following commands to enable the module:

   ```
   php bin/magento module:enable RLTSquare_AjaxShoppingCartUpdate
   php bin/magento setup:upgrade
   php bin/magento cache:clean
   php bin/magento cache:flush
   ```

4. If Magento® is running in production mode, deploy static content: 

   ```
   php bin/magento setup:static-content:deploy
   ```


## Requirements

1. This Magento® extension works on Magento 2.1,2.2 and 2.3 versions. Tested on versions 2.2.5 and above.

2. Tested on different themes specifically Ultimo, Porto and certain custom themes

For details, read our blog:
https://www.rltsquare.com/blog/ajax-shopping-cart-update-magento-2-extension/
