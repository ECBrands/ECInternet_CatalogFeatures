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
use ECInternet\CatalogFeatures\Helper\Data;

/**
 * Plugin for Magento\Catalog\Block\Product\ListProduct
 */
class ListProductPlugin
{
    /**
     * @var \Magento\Framework\App\Response\Http
     */
    private $_response;

    /**
     * @var \ECInternet\CatalogFeatures\Helper\Data
     */
    private $_helper;

    /**
     * ListProductPlugin constructor.
     *
     * @param \Magento\Framework\App\Response\Http    $response
     * @param \ECInternet\CatalogFeatures\Helper\Data $helper
     */
    public function __construct(
        Http $response,
        Data $helper
    ) {
        $this->_response = $response;
        $this->_helper   = $helper;
    }

    /**
     * Redirect to product page if there is only one product in the Category.
     *
     * @param \Magento\Catalog\Block\Product\ListProduct              $subject
     * @param \Magento\Eav\Model\Entity\Collection\AbstractCollection $resultCollection
     *
     * @return \Magento\Eav\Model\Entity\Collection\AbstractCollection $resultCollection
     */
    public function afterGetLoadedProductCollection(
        /** @noinspection PhpUnusedParameterInspection */ ListProduct $subject,
        AbstractCollection $resultCollection
    ) {
        if ($this->_helper->isModuleEnabled()) {
            if ($this->_helper->shouldRedirectForSingleCategoryProduct()) {
                if ($resultCollection->count() === 1) {
                    /** @var \Magento\Catalog\Model\Product $product */
                    $product = $resultCollection->getFirstItem();

                    $this->_response->setRedirect($product->getProductUrl());
                }
            }
        }

        return $resultCollection;
    }
}
