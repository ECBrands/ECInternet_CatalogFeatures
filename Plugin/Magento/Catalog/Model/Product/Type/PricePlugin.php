<?php
/**
 * Copyright (C) EC Brands Corporation - All Rights Reserved
 * Contact Licensing@ECInternet.com for use guidelines
 */
declare(strict_types=1);

namespace ECInternet\CatalogFeatures\Plugin\Magento\Catalog\Model\Product\Type;

use Magento\Catalog\Model\Product\Type\Price;
use ECInternet\CatalogFeatures\Helper\Data;
use ECInternet\CatalogFeatures\Logger\Logger;

/**
 * Plugin for Magento\Catalog\Model\Product\Type\Price
 */
class PricePlugin
{
    /**
     * @var \ECInternet\CatalogFeatures\Helper\Data
     */
    private $_helper;

    /**
     * @var \ECInternet\CatalogFeatures\Logger\Logger
     */
    private $_logger;

    /**
     * PricePlugin constructor.
     *
     * @param \ECInternet\CatalogFeatures\Helper\Data   $helper
     * @param \ECInternet\CatalogFeatures\Logger\Logger $logger
     */
    public function __construct(
        Data $helper,
        Logger $logger
    ) {
        $this->_helper = $helper;
        $this->_logger = $logger;
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
        $this->log('afterGetBasePrice()', [
            'product' => $product->getSku(),
            'qty'     => $qty,
            'price'   => $result
        ]);

        if ($this->_helper->isModuleEnabled()) {
            if ($this->_helper->shouldAlwaysApplyTierPrice()) {
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
        $this->_logger->info('Plugin/Magento/Catalog/Model/Product/Type/PricePlugin - ' . $message, $extra);
    }
}
