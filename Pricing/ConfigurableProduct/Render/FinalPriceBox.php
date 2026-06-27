<?php
/**
 * Copyright (C) EC Brands Corporation - All Rights Reserved
 * Contact Licensing@ECInternet.com for use guidelines
 */
declare(strict_types=1);

namespace ECInternet\CatalogFeatures\Pricing\ConfigurableProduct\Render;

use Magento\Catalog\Model\Product\Pricing\Renderer\SalableResolverInterface;
use Magento\Catalog\Pricing\Price\MinimalPriceCalculatorInterface;
use Magento\ConfigurableProduct\Pricing\Price\ConfigurableOptionsProviderInterface;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Framework\Pricing\Price\PriceInterface;
use Magento\Framework\Pricing\Render\RendererPool;
use Magento\Framework\Pricing\SaleableInterface;
use Magento\Framework\View\Element\Template\Context;
use ECInternet\CatalogFeatures\Model\Config;

/**
 * Pricing Render FinalPriceBox Model
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class FinalPriceBox extends \Magento\ConfigurableProduct\Pricing\Render\FinalPriceBox
{
    /**
     * @var \Magento\Framework\App\Http\Context
     */
    private $httpContext;

    /**
     * @var \ECInternet\CatalogFeatures\Model\Config
     */
    private $config;

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
     * @param \ECInternet\CatalogFeatures\Model\Config                                        $config
     * @param \Magento\Framework\App\Http\Context                                             $httpContext
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
        Config $config,
        HttpContext $httpContext,
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

        $this->config = $config;
        $this->httpContext = $httpContext;
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
        return $this->config->isModuleEnabled() && $this->config->hidePricesForGuests() && !$this->isLoggedIn();
    }

    /**
     * Is current Customer logged in?
     *
     * @return bool
     */
    private function isLoggedIn()
    {
        return (bool)$this->httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH);
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
