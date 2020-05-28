# Product Label


## Installation

##### Using Composer (we recommended)

```
composer require boolfly/module-ajax-compare
```

## 1. Configuration

### General Configuration

Login to Magento Admin, go to **Stores > Configuration > Boolfly > Product Label**

![General Configuration](https://github.com/boolfly/wiki/blob/master/magento/magento2/images/product-label/product-label-01.png)

#### General Settings

##### Enable : 
<ul>
  <li>Select “Yes” to display the product label on front-end.</li>
  <li>Select “No” to hide the product label on front-end.</li>
 </ul>

## 2. Add new rule for the product menu

Login to Magento Admin, go to **Catalog > Manager Rule > Add New**

![New Rule](https://github.com/boolfly/wiki/blob/master/magento/magento2/images/product-label/product-label-02.png)

### General

![New Rule](https://github.com/boolfly/wiki/blob/master/magento/magento2/images/product-label/product-label-03.png)

##### Enable : 
<ul>
  <li>Select “Yes” to turn on rule for the product label.</li>
  <li>Select “No” to turn off rule for the product label.</li>
</ul>

##### Title:
Enter name for the product label.

##### Type: 
<ul>
  <li> New: The product label display with green background.</li>
  <li>Sale: The product label display for products are discounted with red background.</li>
  <li>Best Seller: The product label display with blue background.</li>
  <li>Custom: The product label display without background.</li>
</ul>

##### Description:
Enter note for the rule of product label.

##### Store View:
Select store view to display the product label.

##### Customer Groups:
Select customer groups to apply the rule

##### From:
A beginning date applies the rule.

##### To:
An ending date applies the rule.

##### Priority:
Enter the priority number showing the product label (the smaller number is the higher priority)


### Conditions

![New Rule](https://github.com/boolfly/wiki/blob/master/magento/magento2/images/product-label/product-label-04.png)

![New Rule](https://github.com/boolfly/wiki/blob/master/magento/magento2/images/product-label/product-label-05.png)

Conditions (don't add conditions if rule is applied to all products) : Select the rule to apply for all products


#### Product Listing

##### Is Display:
<ul>
  <li>Select “Yes” to display the product label on the product list page of front-end.</li>
  <li>Select “No” to hide the product label on the product list page of front-end.</li>
</ul>

##### Position:
select position to display the product label.

##### Label Type:
<ul>
  <li>Text: Display text for the product label.</li>
  <li>Image: Display image for the product label.</li>
</ul>

##### Text:
Enter text to display on the product label (only apply when selecting text for label type)

##### Image:
Upload image to display on the product label (only apply when selecting image for label type).

##### Css Style Code:
Style inline css for the product label.

#### Product Page

##### Is Display: 
<ul>
  <li>Select “Yes” to display the product label on the product detail page of front-end.</li>
  <li>Select “No” to hide the product label on the product detail page of front-end.</li>
</ul>

##### Position:
select position to display the product label.

##### Label Type:
<ul>
  <li>Text: Display text for the product label.</li>
  <li>Image: Display image for the product label.</li>
</ul>

##### Text:
Enter text to display on the product label (only apply when selecting text for label type)

##### Image:
Upload image to display on the product label (only apply when selecting image for label type).

##### Css Style Code:
Style inline css for the product label.

## 3. How does it work?

### Product Listing
![New Rule](https://github.com/boolfly/wiki/blob/master/magento/magento2/images/product-label/product-label-06.png)

### Product Page
![New Rule](https://github.com/boolfly/wiki/blob/master/magento/magento2/images/product-label/product-label-07.png)


Contribution
---
Want to contribute to this extension? The quickest way is to open a [pull request on GitHub](https://help.github.com/articles/using-pull-requests)

Magento 2 Extensions
---

- [Ajax Wishlist](https://github.com/boolfly/ajax-wishlist) 
- [Quick View](https://github.com/boolfly/quick-view)
- [Banner Slider](https://github.com/boolfly/banner-slider)
- [Product Label](https://github.com/boolfly/product-label) 
- [ZaloPay](https://github.com/boolfly/zalo-pay) 
- [Momo](https://github.com/boolfly/momo-wallet) 
- [Blog](https://github.com/boolfly/blog)
- [Brand](https://github.com/boolfly/brand) 
- [Product Question](https://github.com/boolfly/product-question) 
- [Sales Sequence](https://github.com/boolfly/sales-sequence) 

Support
---
If you encounter any problems or bugs, please open an issue on [GitHub](https://github.com/boolfly/product-label/issues).

Need help settings up or want to customize this extension to meet your business needs? Please email boolfly.inc@gmail.com and if we like your idea we will add this feature for free or at a discounted rate.

