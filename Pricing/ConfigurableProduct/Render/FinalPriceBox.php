<?php
/**
 * Copyright (C) EC Brands Corporation - All Rights Reserved
 * Contact Licensing@ECInternet.com for use guidelines
 */
declare(strict_types=1);

namespace ECInternet\CatalogFeatures\Pricing\ConfigurableProduct\Render;

use Magento\Catalog\Model\Product\Pricing\Renderer\SalableResolverInterface;
use Magento\Catalog\Pricing\Price\MinimalPriceCalculatorInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Pricing\Price\PriceInterface;
use Magento\Framework\Pricing\Render\RendererPool;
use Magento\Framework\Pricing\SaleableInterface;
use Magento\Framework\View\Element\Template\Context;
use ECInternet\CatalogFeatures\Helper\Data;
use Magento\ConfigurableProduct\Pricing\Price\ConfigurableOptionsProviderInterface;

/**
 * Pricing Render FinalPriceBox Model
 */
class FinalPriceBox extends \Magento\ConfigurableProduct\Pricing\Render\FinalPriceBox
{
    /**
     * @var \ECInternet\CatalogFeatures\Helper\Data
     */
    private $_helper;

    /**
     * FinalPriceBox constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context                                $context
     * @param \Magento\Framework\Pricing\SaleableInterface                                    $saleableItem
     * @param \Magento\Framework\Pricing\Price\PriceInterface                                 $price
     * @param \Magento\Framework\Pricing\Render\RendererPool                                  $rendererPool
     * @param \Magento\Catalog\Model\Product\Pricing\Renderer\SalableResolverInterface        $salableResolver
     * @param \Magento\Catalog\Pricing\Price\MinimalPriceCalculatorInterface                  $minimalPriceCalculator
     * @param \Magento\ConfigurableProduct\Pricing\Price\ConfigurableOptionsProviderInterface $configurableOptionsProvider
     * @param \ECInternet\CatalogFeatures\Helper\Data                                         $helper
     * @param array                                                                           $data
     */
    public function __construct(
        Context $context,
        SaleableInterface $saleableItem,
        PriceInterface $price,
        RendererPool $rendererPool,
        SalableResolverInterface $salableResolver,
        MinimalPriceCalculatorInterface $minimalPriceCalculator,
        ConfigurableOptionsProviderInterface $configurableOptionsProvider,
        Data $helper,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $saleableItem,
            $price,
            $rendererPool,
            $salableResolver,
            $minimalPriceCalculator,
            $configurableOptionsProvider,
            $data
        );

        $this->_helper = $helper;
    }

    /**
     * @inheritDoc
     */
    protected function wrapResult($html)
    {
        if ($this->shouldHidePricesForGuests()) {
            return '<div class="" data-role="priceBox" data-product-id="' . $this->getId() . '"></div>';
        }

        return parent::wrapResult($html);
    }

    /**
     * Should we hide prices for guests?
     *
     * @return bool
     */
    private function shouldHidePricesForGuests()
    {
        return $this->_helper->isModuleEnabled() && $this->_helper->hidePricesForGuests() && !$this->isLoggedIn();
    }

    /**
     * Is current Customer logged in?
     *
     * @return bool
     */
    private function isLoggedIn()
    {
        $objectManager = ObjectManager::getInstance();
        $httpContext = $objectManager->get(\Magento\Framework\App\Http\Context::class);

        return $httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH);
    }

    /**
     * Get the Id of the current Saleable item.
     *
     * @return int
     */
    private function getId()
    {
        return $this->getSaleableItem()->getId();
    }
}
