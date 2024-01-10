<?php
/**
 * Copyright (C) EC Brands Corporation - All Rights Reserved
 * Contact Licensing@ECInternet.com for use guidelines
 */
declare(strict_types=1);

namespace ECInternet\CatalogFeatures\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

/**
 * Helper
 */
class Data extends AbstractHelper
{
    const CONFIG_PATH_ENABLED                         = 'catalog_features/general/enable';

    const CONFIG_PATH_HIDE_PRICE_FLAG                 = 'catalog_features/catalog/hide_prices_for_guests';

    const CONFIG_PATH_HIDE_EMPTY_CATEGORIES           = 'catalog_features/catalog/hide_empty_categories';

    const CONFIG_PATH_REDIRECT_404_PAGES              = 'catalog_features/catalog/redirect_to_search';

    const CONFIG_PATH_REDIRECT_TITLE                  = 'catalog_features/catalog/redirect_search_title';

    const CONFIG_PATH_DISABLED_URL                    = 'catalog_features/catalog/redirect_disabled_url';

    const CONFIG_PATH_REDIRECT_HOME                   = 'catalog_features/catalog/redirect_to_homepage';

    const CONFIG_PATH_REDIRECT_SINGLE_PRODUCT         = 'catalog_features/category/redirect_single';

    const CONFIG_PATH_ALWAYS_APPLY_TIERPRICE          = 'catalog_features/catalog/always_apply_tierprice';

    const CONFIG_PATH_CONFIGURABLE_REDIRECT           = 'catalog_features/configurable_products/redirect_simple_to_configurable';

    const CONFIG_PATH_MISSING_IMAGE_REPORT_RECIPIENTS = 'catalog_features/missing_image_report/email_recipients';

    const URL_PARAM_IS_404_SEARCH                     = 'is404';

    /**
     * Is extension enabled?
     *
     * @return bool
     */
    public function isModuleEnabled()
    {
        return $this->scopeConfig->isSetFlag(self::CONFIG_PATH_ENABLED);
    }

    /**
     * Should we hide prices for guests?
     *
     * @return bool
     */
    public function hidePricesForGuests()
    {
        return $this->scopeConfig->isSetFlag(self::CONFIG_PATH_HIDE_PRICE_FLAG);
    }

    /**
     * Should we hide categories when they have no products?
     *
     * @return bool
     */
    public function hideEmptyCategories()
    {
        return $this->scopeConfig->isSetFlag(self::CONFIG_PATH_HIDE_EMPTY_CATEGORIES);
    }

    /**
     * Should we redirect to the search page when a user hits a 404?
     *
     * @return bool
     */
    public function shouldRedirectToSearchFor404Pages()
    {
        return $this->scopeConfig->isSetFlag(self::CONFIG_PATH_REDIRECT_404_PAGES);
    }

    /**
     * Get the setting value for the search title for a 404 redirect
     *
     * @return string
     */
    public function getRedirectSearchTitle()
    {
        return $this->scopeConfig->getValue(self::CONFIG_PATH_REDIRECT_TITLE);
    }

    /**
     * Get the setting value for the redirect path for a disabled product
     *
     * @return string
     */
    public function getRedirectDisabledUrlPath()
    {
        return $this->scopeConfig->getValue(self::CONFIG_PATH_DISABLED_URL);
    }

    /**
     * Should we redirect 404s to the homepage?
     *
     * @return bool
     */
    public function shouldRedirectToHomepage()
    {
        return $this->scopeConfig->getValue(self::CONFIG_PATH_REDIRECT_HOME);
    }

    /**
     * Should we apply the tier price over the base price?
     *
     * @return bool
     */
    public function shouldAlwaysApplyTierPrice()
    {
        return $this->scopeConfig->getValue(self::CONFIG_PATH_ALWAYS_APPLY_TIERPRICE);
    }

    /**
     * Should we redirect the user to the product page when he visits a category page with only one product?
     *
     * @return bool
     */
    public function shouldRedirectForSingleCategoryProduct()
    {
        return $this->scopeConfig->isSetFlag(self::CONFIG_PATH_REDIRECT_SINGLE_PRODUCT);
    }

    /**
     * Should we redirect the user to the ConfigurableProduct page if he attempts to visit a simple product page?
     *
     * @return bool
     */
    public function shouldRedirectSimpleToConfigurable()
    {
        return $this->scopeConfig->isSetFlag(self::CONFIG_PATH_CONFIGURABLE_REDIRECT);
    }

    /**
     * @return string
     */
    public function getMissingImageReportRecipients()
    {
        return (string)$this->scopeConfig->getValue(self::CONFIG_PATH_MISSING_IMAGE_REPORT_RECIPIENTS);
    }

    /**
     * Should we redirect the user to a custom page for disabled products?
     *
     * @return bool
     */
    public function shouldRedirectToCustomPageForDisabledProducts()
    {
        if ($this->shouldRedirectToSearchFor404Pages()) {
            return !empty($this->getRedirectDisabledUrlPath());
        }

        return false;
    }
}
