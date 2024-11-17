=== Reav Buy and Save for WooCommerce ===
Contributors: reavdev
Tags: woocommerce, subscriptions, discounts, variable-products, tax-handling
Requires at least: 5.0
Tested up to: 6.2
Stable tag: 1.0.0
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Reav Buy and Save allows customers to choose between a one-time purchase or a discounted subscription for your WooCommerce products.

== Description ==

**Reav Buy and Save** enhances your WooCommerce store by offering customers the flexibility to either make a one-time purchase or subscribe and save. It provides:

- **Subscription Options:** Define global or per-product subscription plans with custom frequencies and discounts.
- **Tax Handling:** Correctly calculates and displays prices including or excluding taxes based on your WooCommerce settings.
- **Variable Products Support:** Automatically updates prices when customers select different product variations.
- **Seamless Integration:** Works with WooCommerce Subscriptions to manage recurring payments and subscriptions.

## Features

- **One-time or Subscription Purchase:** Allow customers to choose between purchasing a product once or subscribing for regular deliveries at a discounted price.
- **Customizable Subscription Plans:** Define multiple subscription plans with different frequencies (e.g., weekly, monthly) and discount rates.
- **Per-Product and Global Settings:** Set subscription options globally or override them on a per-product basis.
- **Dynamic Price Updates:** Prices update in real-time based on variation selections and active sales.
- **Tax Compliance:** Prices are calculated and displayed according to WooCommerce's tax settings.
- **Admin Interface:** Easy-to-use admin settings page to manage subscriptions.

## Installation

1. **Upload the Plugin Files:**
   - Upload the `reav-buy-and-save` folder to the `/wp-content/plugins/` directory.

2. **Activate the Plugin:**
   - Navigate to the 'Plugins' menu in WordPress and activate the 'Reav Buy and Save for WooCommerce' plugin.

3. **Requirements:**
   - Ensure that **WooCommerce** and **WooCommerce Subscriptions** are installed and activated.

## Usage

### Global Subscription Settings

1. **Navigate to the Settings Page:**
   - Go to **WooCommerce > Reav Buy and Save**.

2. **Add Subscription Plans:**
   - Under **Global Subscription Types**, add your subscription plans.
   - Define the name, frequency (number and period), discount percentage, and any features.

3. **Save Settings:**
   - Click **Save Changes** to store your subscription plans.

### Per-Product Subscription Settings

1. **Edit a Product:**
   - Go to **Products > All Products** and edit the product you want to customize.

2. **Override Global Subscriptions:**
   - In the **Product Data** section, under **General**, check the **Override Global Subscriptions** option.

3. **Add Subscription Plans:**
   - Add custom subscription plans specific to this product.

4. **Save the Product:**
   - Click **Update** to save your changes.

### Variable Products Support

- **Price Updates:**
  - When customers select different variations, the one-time and subscription prices will automatically update to reflect the selected variation's price and any applicable discounts.

### Tax Handling

- **Automatic Calculation:**
  - Prices displayed to customers will include or exclude tax based on your WooCommerce settings.
  - The plugin uses WooCommerce functions to ensure taxes are correctly applied in price calculations.

## Frequently Asked Questions

= Do I need WooCommerce Subscriptions for this plugin to work? =

Yes, WooCommerce Subscriptions must be installed and activated for the subscription functionality to work.

= Does this plugin support variable products? =

Yes, the plugin supports variable products and updates prices when variations are changed.

= How are taxes handled? =

The plugin uses WooCommerce's tax settings and functions to ensure that prices are calculated and displayed correctly, including or excluding taxes as configured.

= What if a product is on sale? =

The plugin automatically uses the sale price for both one-time purchases and subscriptions if the product is on sale.

## Screenshots

1. **Global Subscription Settings:** Manage subscription plans that apply to all products.
2. **Per-Product Subscription Settings:** Override global subscriptions with custom plans for individual products.
3. **Product Page Integration:** Choose between one-time purchase and subscription with dynamic price updates.
4. **Cart and Checkout:** Display selected purchase type and subscription details.

## Changelog

= 1.0.0 =
* Initial release.
* Added global and per-product subscription options.
* Implemented tax handling in price calculations.
* Added support for variable products with dynamic price updates.
* Integrated with WooCommerce Subscriptions.

## Upgrade Notice

= 1.0.0 =
- Ensure that you have WooCommerce and WooCommerce Subscriptions installed and activated before using this plugin.
