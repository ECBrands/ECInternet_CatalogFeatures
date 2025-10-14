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
    /**
     * Urldecode request value, then replace '-' with ' ' and '.html' with ''
     *
     * @param string $requestValue
     *
     * @return string
     */
    public function cleanRequestValue(string $requestValue)
    {
        return str_replace(['.html', '-'], ['', ' '], urldecode($requestValue));
    }
}
