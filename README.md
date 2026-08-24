# SR_ProductAttributeOptionSorting

Magento 2 module that adds attribute option sorting behavior for product attributes in Admin.

## Purpose

This module extends product attribute option handling so option sorting mode can be managed in Admin and applied consistently after attribute save.

## Features

- Adds admin integration for product attribute option sorting behavior
- Persists sorting mode on attribute save
- Applies sorting logic via service/observer flow

## Requirements

- Magento 2 (Adobe Commerce / Magento Open Source)
- PHP version compatible with your Magento installation

## Installation

### Composer (recommended)

```bash
composer require studioraz/magento2-product-attribute-option-sorting:^1.0
bin/magento module:enable SR_ProductAttributeOptionSorting
bin/magento setup:upgrade
bin/magento cache:flush
```

### Manual (from source)

1. Place module files under:
   `app/code/SR/ProductAttributeOptionSorting`
2. Run:

```bash
bin/magento module:enable SR_ProductAttributeOptionSorting
bin/magento setup:upgrade
bin/magento cache:flush
```

## Repository layout

- Module source code lives under `src/`

## Module name

- `SR_ProductAttributeOptionSorting`
