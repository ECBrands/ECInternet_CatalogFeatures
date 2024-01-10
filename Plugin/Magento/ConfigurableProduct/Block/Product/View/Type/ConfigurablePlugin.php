<?php
/**
 * Copyright (C) EC Brands Corporation - All Rights Reserved
 * Contact Licensing@ECInternet.com for use guidelines
 */
declare(strict_types=1);

namespace ECInternet\CatalogFeatures\Plugin\Magento\ConfigurableProduct\Block\Product\View\Type;

use Magento\ConfigurableProduct\Block\Product\View\Type\Configurable;

/**
 * Plugin for Magento\ConfigurableProduct\Block\Product\View\Type\Configurable
 */
class ConfigurablePlugin
{
    /**
     * Add simple product skus and names to ConfigurableProduct JSON config.
     *
     * @param \Magento\ConfigurableProduct\Block\Product\View\Type\Configurable $subject
     * @param string                                                            $result
     *
     * @return string
     */
    public function afterGetJsonConfig(
        Configurable $subject,
        string $result
    ) {
        // Decode existing JSON into an associate array
        $jsonResult = json_decode($result, true);

        $jsonResult['skus'] = [];
        foreach ($subject->getAllowProducts() as $simpleProduct) {
            $jsonResult['skus'][$simpleProduct->getId()] = $simpleProduct->getSku();
            $jsonResult['names'][$simpleProduct->getId()] = $simpleProduct->getName();
        }

        // Re-encode JSON
        return json_encode($jsonResult);
    }
}
