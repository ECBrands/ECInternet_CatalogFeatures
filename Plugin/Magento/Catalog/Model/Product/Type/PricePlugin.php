<?php
/**
 * Copyright (C) EC Brands Corporation - All Rights Reserved
 * Contact Licensing@ECInternet.com for use guidelines
 */
declare(strict_types=1);

namespace ECInternet\CatalogFeatures\Plugin\Magento\Catalog\Model\Product\Type;

use Magento\Catalog\Model\Product\Type\Price;
use ECInternet\CatalogFeatures\Model\Config;
use Psr\Log\LoggerInterface;

/**
 * Plugin for Magento\Catalog\Model\Product\Type\Price
 */
class PricePlugin
{
    /**
     * @var \ECInternet\CatalogFeatures\Model\Config
     */
    private $config;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * PricePlugin constructor.
     *
     * @param \ECInternet\CatalogFeatures\Model\Config $config
     * @param \Psr\Log\LoggerInterface                 $logger
     */
    public function __construct(
        Config $config,
        LoggerInterface $logger
    ) {
        $this->config = $config;
        $this->logger = $logger;
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
     *
     * @noinspection PhpMissingParamTypeInspection
     */
    public function afterGetBasePrice(
        Price $subject,
        float $result,
        $product,
        $qty = null
    ) {
        $this->log('afterGetBasePrice()', [
            'product' => $product->getSku(),
            'qty'     => $qty,
            'price'   => $result
        ]);

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
