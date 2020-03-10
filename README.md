# Omneo Magento Module
A native Magento module to handle data webhooks, public product image access, shapes tokens and 

## Fetch product image
The Omneo module exposes a public endpoint for retrieving the primary image for a product, by SKU.

This endpoint takes an `sku` parameter for finding the product and a `size` parameter for setting the width of the returned image in pixels. The default image size is `400px` wide. All images use Magento 2's caching methods for best performance. 

### Requesting an image
Use the following url format in place of any regular image url path:
```
{{base_url}}/omneo/product/image?sku={{sku}}&size={{size}}
```

Here's an example of the complete url:
```
https://mywebsite.com/omneo/product/image?sku=SKU1000005&size=250
```