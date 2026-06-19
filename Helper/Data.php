<?php
/**
 * Copyright (C) EC Brands Corporation - All Rights Reserved
 * Contact Licensing@ECInternet.com for use guidelines
 */
declare(strict_types=1);

namespace ECInternet\CatalogFeatures\Helper;

/**
 * Helper
 */
class Data
{
    /**
     * Urldecode request value, then replace '-' with ' ' and '.html' with ''
     *
     * @param string $requestValue
     *
     * @return string
     */
    public static function cleanRequestValue(string $requestValue)
    {
        return str_replace(['.html', '-'], ['', ' '], urldecode($requestValue));
    }
}
