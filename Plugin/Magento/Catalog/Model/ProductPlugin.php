<?php
/**
 * Copyright (C) EC Brands Corporation - All Rights Reserved
 * Contact Licensing@ECInternet.com for use guidelines
 */
declare(strict_types=1);

namespace ECInternet\CatalogFeatures\Plugin\Magento\Catalog\Model;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Framework\App\Response\Http as HttpResponse;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\UrlInterface;
use ECInternet\CatalogFeatures\Helper\Data;
use ECInternet\CatalogFeatures\Logger\Logger;
use ECInternet\CatalogFeatures\Model\Config;

/**
 * Plugin for Magento\Catalog\Model\Product
 */
class ProductPlugin
{
    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var \Magento\ConfigurableProduct\Model\Product\Type\Configurable
     */
    private $catalogProductTypeConfigurable;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    private $request;

    /**
     * @var \Magento\Framework\App\ResponseInterface
     */
    private $response;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    private $urlInterface;

    private $helper;

    /**
     * @var \ECInternet\CatalogFeatures\Logger\Logger
     */
    private $logger;

    /**
     * @var \ECInternet\CatalogFeatures\Model\Config
     */
    private $config;

    /**
     * CatalogProductPlugin constructor.
     *
     * @param \Magento\Catalog\Api\ProductRepositoryInterface              $productRepository
     * @param \Magento\ConfigurableProduct\Model\Product\Type\Configurable $catalogProductTypeConfigurable
     * @param \Magento\Framework\App\Request\Http                          $request
     * @param \Magento\Framework\App\ResponseInterface                     $response
     * @param \Magento\Framework\UrlInterface                              $urlInterface
     * @param \ECInternet\CatalogFeatures\Helper\Data                      $helper
     * @param \ECInternet\CatalogFeatures\Logger\Logger                    $logger
     * @param \ECInternet\CatalogFeatures\Model\Config                     $config
     */
    public function __construct(
        ProductRepositoryInterface $productRepository,
        Configurable $catalogProductTypeConfigurable,
        HttpRequest $request,
        ResponseInterface $response,
        UrlInterface $urlInterface,
        Data $helper,
        Logger $logger,
        Config $config
    ) {
        $this->productRepository              = $productRepository;
        $this->catalogProductTypeConfigurable = $catalogProductTypeConfigurable;
        $this->request                        = $request;
        $this->response                       = $response;
        $this->urlInterface                   = $urlInterface;
        $this->helper                         = $helper;
        $this->logger                         = $logger;
        $this->config                         = $config;
    }

    /**
     * Build URL which points to specific Product configuration represented by values in current Simple Product
     *
     * @param \Magento\Catalog\Model\Product $subject
     * @param string                         $result
     * @param bool                           $useSid
     *
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function afterGetProductUrl(
        Product $subject,
        string $result,
        /* @noinspection PhpMissingParamTypeInspection PhpUnusedParameterInspection */ $useSid = null
    ) {
        if (!$this->config->isModuleEnabled()) {
            return $result;
        }

        if (!$this->config->shouldRedirectSimpleToConfigurable()) {
            return $result;
        }

        if ($this->isConfigurable($subject)) {
            return $result;
        }

        // Cache productId
        $productId = $subject->getId();

        if (!$productId) {
            $this->log('afterGetProductUrl() - Product->getId() returned a falsy value (' . $subject->getId() . ')');
            return $result;
        }

        if (!is_numeric($productId)) {
            $this->log('afterGetProductUrl() - Product->getId() returned a non-numeric value (' . $productId . ')');
            return $result;
        }

        // Gets array of parent productIds if this product is used as a configurable
        /** @var \Magento\Catalog\Api\Data\ProductInterface $parentProduct */
        $parentProduct = $this->getFirstParentProduct((int)$productId);

        if (!$parentProduct) {
            return $result;
        }

        if (!$parentProduct instanceof Product) {
            return $result;
        }

        // Get the attributes which make this product configurable and construct new product url
        if ($configurableAttributes = $this->getConfigurableAttributes($parentProduct)) {
            if (is_array($configurableAttributes)) {
                /** @var \Magento\Catalog\Api\Data\ProductInterface $product */
                if ($product = $this->productRepository->getById($productId)) {
                    if ($urlPairs = $this->buildConfigurableUrlPairs($product, $configurableAttributes)) {
                        $result = $parentProduct->getProductUrl() . '#' . implode('&amp;', $urlPairs);
                    }
                }
            }
        }

        // If nothing done, return result (required in afterXXX Plugins)
        return $result;
    }

    /**
     * Redirect user to search page if product is disabled
     *
     * @param \Magento\Catalog\Model\Product $subject
     * @param int                            $result
     *
     * @return int
     */
    public function afterGetStatus(
        Product $subject,
        /* @noinspection PhpMissingParamTypeInspection */ $result
    ) {
        if (!$this->config->isModuleEnabled()) {
            return $result;
        }

        if (!$this->config->shouldRedirectToSearchFor404Pages()) {
            return $result;
        }

        // Logic should only run on product view request
        if (!$this->isProductViewRequest()) {
            return $result;
        }

        // Enabled products don't need redirect
        if ($result != Status::STATUS_DISABLED) {
            return $result;
        }

        // Cleanup product 'url_key'
        $urlKey = $this->helper->cleanRequestValue($subject->getUrlKey());

        if (empty($urlKey)) {
            return $result;
        }

        if (!$this->response instanceof HttpResponse) {
            return $result;
        }

        if ($this->shouldRedirectToCustomPageForDisabledProducts()) {
            $customPath = $this->urlInterface->getUrl($this->config->getRedirectDisabledUrlPath());
            $this->response->setRedirect($customPath)->sendResponse();
        } else {
            $queryParams = [
                'q'                             => $urlKey,
                Config::URL_PARAM_IS_404_SEARCH => true
            ];

            $searchUrl = $this->urlInterface
                ->addQueryParams($queryParams)
                ->getUrl('catalogsearch/result');

            $this->response->setRedirect($searchUrl)->sendResponse();
        }

        return $result;
    }

    /**
     * Is product type configurable?
     *
     * @param \Magento\Catalog\Model\Product $product
     *
     * @return bool
     */
    private function isConfigurable(Product $product)
    {
        return $product->getTypeId() == Configurable::TYPE_CODE;
    }

    /**
     * Retrieve the first ConfigurableProduct parent of a Simple product.
     *
     * @param int $productId
     *
     * @return \Magento\Catalog\Api\Data\ProductInterface|null
     */
    private function getFirstParentProduct(int $productId)
    {
        $parentIds = $this->getParentIds($productId);
        if (count($parentIds) > 0) {
            // Use first result
            $productId = $parentIds[0];

            if (is_numeric($productId)) {
                return $this->getProductById((int)$productId);
            }
        }

        return null;
    }

    /**
     * Retrieve parent ids array for productId.
     *
     * @param int $childProductId
     *
     * @return string[]
     */
    private function getParentIds(int $childProductId)
    {
        return $this->catalogProductTypeConfigurable->getParentIdsByChild($childProductId);
    }

    /**
     * Retrieve configurable attribute data for product.
     *
     * @param \Magento\Catalog\Model\Product $configurableProduct
     *
     * @return \Magento\ConfigurableProduct\Model\Product\Type\Configurable\Attribute[]
     */
    private function getConfigurableAttributes(
        Product $configurableProduct
    ) {
        return $this->catalogProductTypeConfigurable->getConfigurableAttributes($configurableProduct);
    }

    private function getProductById(int $productId)
    {
        try {
            return $this->productRepository->getById($productId);
        } catch (NoSuchEntityException $e) {
            $this->log('getProductById()', ['productId' => $productId, 'exception' => $e->getMessage()]);
        }

        return null;
    }

    /**
     * Build query string param pairs
     *
     * @param \Magento\Catalog\Api\Data\ProductInterface                               $product
     * @param \Magento\ConfigurableProduct\Model\Product\Type\Configurable\Attribute[] $configurableAttributes
     *
     * @return array
     */
    private function buildConfigurableUrlPairs(
        ProductInterface $product,
        array $configurableAttributes
    ) {
        /** @var \Magento\Eav\Model\Entity\Attribute\AbstractAttribute[] $productAttributes */
        $productAttributes = $product->getAttributes();

        $pieces = [];

        // Iterate over configurable attributes
        foreach ($configurableAttributes as $configurableAttribute) {
            foreach ($productAttributes as $productAttribute) {
                if ($productAttribute->getAttributeId() == $configurableAttribute->getAttributeId()) {
                    $configurableAttributeCode = $productAttribute->getAttributeCode();
                    if ($product instanceof Product) {
                        if ($product->hasData($configurableAttribute)) {
                            $key = $configurableAttribute->getAttributeId();
                            $val = $product->getData($configurableAttributeCode);

                            $pieces[] = "$key=$val";
                        }
                    }
                }
            }
        }

        return $pieces;
    }

    /**
     * Is the customer viewing the Product/View page?
     *
     * @return bool
     */
    private function isProductViewRequest()
    {
        return ($this->request->getControllerName() == 'product' && $this->request->getActionName() == 'view');
    }

    /**
     * Should we redirect the user to a custom page for disabled products?
     *
     * @return bool
     */
    private function shouldRedirectToCustomPageForDisabledProducts()
    {
        if ($this->config->shouldRedirectToSearchFor404Pages()) {
            return !empty($this->config->getRedirectDisabledUrlPath());
        }

        return false;
    }

    /**
     * Write to extension log
     *
     * @param string $message
     * @param array  $extra
     */
    private function log(string $message, array $extra = [])
    {
        $this->logger->info('Plugin/Magento/Catalog/Model/ProductPlugin - ' . $message, $extra);
    }
}
