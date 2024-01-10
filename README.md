# Magento2 Module ECInternet CatalogFeatures
``ecinternet/catalog_features - 1.1.10.0``

- [Requirements](#requirements-header)
- [Overview](#overview-header)
- [Installation](#installation-header)
- [Configuration](#configuration-header)
- [Specifications](#specifications-header)
- [Attributes](#attributes-header)
- [Notes](#notes-header)
- [Version History](#version-history-header)

## Requirements

## Overview

## Installation
- Unzip the zip file in `app/code/ECInternet`
- Enable the module by running `php bin/magento module:enable ECInternet_CatalogFeatures`
- Apply database updates by running `php bin/magento setup:upgrade`
- Recompile code by running `php bin/magento setup:di:compile`
- Flush the cache by running `php bin/magento cache:flush`

## Configuration
- Hide frontend prices for guests.
- Automatically redirect to product search for 404 errors.
- Ability to set the page title for a 404 redirect
- Automatically redirect to product pages for single-product categories.
- Automatically redirect to configurable product when user attempts to view simple child product. 

## Specifications

## Attributes
- Product - Allow On Web (`allow_on_web`)
- Product - Seasonal (`seasonal`)

## Notes

## Version History
- 1.1.10.0 - Removed Category attribute Custom URL (`ecin_custom_url`)
- 1.1.9.0 - Set `allow_on_web` as not required. 
- 1.1.7.1 - Fix broke admin category page.
