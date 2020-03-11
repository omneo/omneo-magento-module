# Omneo Magento Module
A native Magento module to handle data webhooks, product image and detail page access and Omneo Shapes tokens.

## Getting Started
### 1. Get the code
Clone or download this Repo and upload to the following path, relative to your Magento 2 root: `{root}/app/code/Omneo/Relay`

### 2. Register custom variables
Within the Magento 2 admin navigate to `System > Other Settings > Custom Variables` and register the following custom fields:

| Variable Code  | Variable Name |
| —————— | —————— |
| omneo_tenant | Omneo Tenant  |
| omneo_webhook_url | Omneo Webhook URL |
| omneo_webhook_secret | Omneo Webhook Secret |
| omneo_id_token | Omneo ID Token |

Talk to your Omneo integration partner for the values for each of these fields. Once you have the values, they should be entered into the `Variable Plain Value` field of the custom variable page.

### 3. Run Magento Setup Upgrade
Once installed and the fields have been configured, run Magento’s functions for code upgrade, compilation, cache invalidation and reindexing.

```sh
$ php bin/magento setup:upgrade
$ php bin/magento setup:di:compile
$ php bin/magento cache:flush
$ php bin/magento indexer:reindex
```

### 4. All done!
Test one of the product URLs mentioned below, to ensure the module is working correctly. You may need to communicate with your Omneo integration partner to ensure webhooks are being received.

For logged in customers, who exist in Omneo, the module will add a new `window.omneoId` variable, including `token` and `expiry` values for Omneo Shapes. You can test that this exists by opening a browser console, typing `window.omneoId`  and pressing enter. It should output the following:

```json
{
	"token":"xxx",
	"expiry":"xxx"
}
```


## Data Webhooks
To ensure Omneo is kept up to date with changes on the Magento site, the module sends webhooks for Product, Order and Customer updates. These are based on the following Magento events:
* customer_account_edited
* customer_register_success
* adminhtml_customer_save_after
* customer_address_save_after
* catalog_product_new_action
* catalog_product_edit_action
* controller_action_catalog_product_save_entity_after
* sales_order_place_after
* order_cancel_after

## Product Helpers
To help external interfaces with linking to products and images, the Omneo Relay module includes an endpoints to return product images and redirect to products, based on SKU. This will primary be used for Omneo interfaces to ensure product images and detail pages can be linked to, even the Magento ID or URL for product changes.

#### Product Images
The module exposes a public endpoint for retrieving the primary image for a product, by SKU.

This endpoint takes an `sku` parameter for finding the product and a `size` parameter for setting the width of the returned image in pixels. The default image size is `400px` wide. All images use Magento 2’s caching methods for best performance. 

##### Requesting an image
Use the following url format in place of any regular image url path:
```
{{base_url}}/omneo/product/image?sku={{sku}}&size={{size}}
```

#### Product URLs
The module’s product route finds product based on SKU and redirects visitors to the live product page.

##### Requesting a product
Use the following url format in place of a product detail url:
```
{{base_url}}/omneo/product/detail?sku={{sku}}
```