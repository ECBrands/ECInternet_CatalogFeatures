<?php
/**
 * Copyright (C) EC Brands Corporation - All Rights Reserved
 * Contact Licensing@ECInternet.com for use guidelines
 */
declare(strict_types=1);

namespace ECInternet\CatalogFeatures\Plugin\Magento\Catalog\Block\Product;

use Magento\Catalog\Block\Product\ListProduct;
use Magento\Eav\Model\Entity\Collection\AbstractCollection;
use Magento\Framework\App\Response\Http;
use ECInternet\CatalogFeatures\Model\Config;

/**
 * Plugin for Magento\Catalog\Block\Product\ListProduct
 */
class ListProductPlugin
{
    /**
     * @var \Magento\Framework\App\Response\Http
     */
    private $response;

    /**
     * @var \ECInternet\CatalogFeatures\Model\Config
     */
    private $config;

    /**
     * ListProductPlugin constructor.
     *
     * @param \Magento\Framework\App\Response\Http     $response
     * @param \ECInternet\CatalogFeatures\Model\Config $config
     */
    public function __construct(
        Http $response,
        Config $config
    ) {
        $this->response = $response;
        $this->config   = $config;
    }

    /**
     * Redirect to product page if there is only one product in the Category.
     *
     * @param \Magento\Catalog\Block\Product\ListProduct              $subject
     * @param \Magento\Eav\Model\Entity\Collection\AbstractCollection $resultCollection
     *
     * @return \Magento\Eav\Model\Entity\Collection\AbstractCollection $resultCollection
     *
     * @noinspection PhpUnusedParameterInspection
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetLoadedProductCollection(
        ListProduct $subject,
        AbstractCollection $resultCollection
    ) {
        if ($this->config->isModuleEnabled()) {
            if ($this->config->shouldRedirectForSingleCategoryProduct()) {
                if ($resultCollection->count() === 1) {
                    /** @var \Magento\Catalog\Model\Product $product */
                    $product = $resultCollection->getFirstItem();

                    $this->response->setRedirect($product->getProductUrl());
                }
            }
        }

        return $resultCollection;
    }
}
