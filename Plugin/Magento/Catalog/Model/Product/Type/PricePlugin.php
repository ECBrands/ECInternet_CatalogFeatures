<?php
/**
 * Copyright (C) EC Brands Corporation - All Rights Reserved
 * Contact Licensing@ECInternet.com for use guidelines
 */
declare(strict_types=1);

namespace ECInternet\CatalogFeatures\Plugin\Magento\Catalog\Model\Product\Type;

use Magento\Catalog\Model\Product\Type\Price;
use ECInternet\CatalogFeatures\Logger\Logger;
use ECInternet\CatalogFeatures\Model\Config;

/**
 * Plugin for Magento\Catalog\Model\Product\Type\Price
 */
class PricePlugin
{
    /**
     * @var \ECInternet\CatalogFeatures\Logger\Logger
     */
    private $logger;

    /**
     * @var \ECInternet\CatalogFeatures\Model\Config
     */
    private $config;

    /**
     * PricePlugin constructor.
     *
     * @param \ECInternet\CatalogFeatures\Logger\Logger $logger
     * @param \ECInternet\CatalogFeatures\Model\Config  $config
     */
    public function __construct(
        Logger $logger,
        Config $config
    ) {
        $this->logger = $logger;
        $this->config = $config;
    }

    /**
     * Possibly override price with tierprice
     *
     * @param \Magento\Catalog\Model\Product\Type\Price $subject
     * @param float                                     $result
     * @param \Magento\Catalog\Model\Product            $product
     * @param float|null                                $qty
     *
     * @return float
     */
    public function afterGetBasePrice(
        Price $subject,
        float $result,
        /* @noinspection PhpMissingParamTypeInspection */ $product,
        /* @noinspection PhpMissingParamTypeInspection */ $qty = null
    ) {
        if ($this->config->isModuleEnabled()) {
            if ($this->config->shouldAlwaysApplyTierPrice()) {
                $tierPrice = $subject->getTierPrice($qty, $product);
                if (is_numeric($tierPrice)) {
                    $this->log("afterGetBasePrice() - Overriding Magento price of: [$result] with tierPrice: [$tierPrice]");

                    return $tierPrice;
                }
            }
        }

        return $result;
    }

    /**
     * Write to extension log
     *
     * @param string $message
     * @param array  $extra
     */
    private function log(string $message, array $extra = [])
    {
        $this->logger->info('Plugin/Magento/Catalog/Model/Product/Type/PricePlugin - ' . $message, $extra);
    }
}
