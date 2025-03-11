<?php
/**
 * Copyright (C) EC Brands Corporation - All Rights Reserved
 * Contact Licensing@ECInternet.com for use guidelines
 */
declare(strict_types=1);

namespace ECInternet\CatalogFeatures\App\Router;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Router\NoRouteHandlerInterface;
use Magento\Framework\Filesystem\Io\File;
use Magento\Framework\Http\PhpEnvironment\Request as HttpRequest;
use ECInternet\CatalogFeatures\Helper\Data;
use ECInternet\CatalogFeatures\Logger\Logger;
use ECInternet\CatalogFeatures\Model\Config;
use Exception;

/**
 * Handler for NoRoute
 */
class NoRouteHandler extends \Magento\Framework\App\Router\NoRouteHandler implements NoRouteHandlerInterface
{
    /**
     * @var \Magento\Framework\Filesystem\Io\File
     */
    private $file;

    /**
     * @var \ECInternet\CatalogFeatures\Helper\Data
     */
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
     * NoRouteHandler constructor.
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Filesystem\Io\File              $file
     * @param \ECInternet\CatalogFeatures\Helper\Data            $helper
     * @param \ECInternet\CatalogFeatures\Logger\Logger          $logger
     * @param \ECInternet\CatalogFeatures\Model\Config           $config
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        File $file,
        Data $helper,
        Logger $logger,
        Config $config
    ) {
        parent::__construct($scopeConfig);

        $this->file   = $file;
        $this->helper = $helper;
        $this->logger = $logger;
        $this->config = $config;
    }

    public function process(
        RequestInterface $request
    ) {
        if (!$this->config->isModuleEnabled()) {
            return parent::process($request);
        }

        // Check if this is a product or category page and redirect to search instead.
        if (!$this->config->shouldRedirectToSearchFor404Pages()) {
            return parent::process($request);
        }

        if (!$request instanceof HttpRequest) {
            return parent::process($request);
        }

        try {
            $requestValue = $this->baseName($request->getPathInfo());

            if (str_contains($requestValue, '.html')) {
                if ($productName = $this->helper->cleanRequestValue($requestValue)) {
                    $request
                        ->setParams(['q' => $productName, Config::URL_PARAM_IS_404_SEARCH => true])
                        ->setModuleName('catalogsearch')
                        ->setControllerName('result')
                        ->setActionName('index');

                    return true;
                }
            }
        } catch (Exception $e) {
            $this->log('process()', ['exception' => $e->getMessage()]);
        }

        // Stock behavior.
        return parent::process($request);
    }

    /**
     * Refactor basename()
     *
     * @param string $path
     *
     * @return string
     */
    private function baseName(string $path)
    {
        // TODO: Try this: https://magento.stackexchange.com/a/145724
        $fileInfo = $this->file->getPathInfo($path);

        return $fileInfo['basename'];
    }

    /**
     * Write to extension log
     *
     * @param string $message
     * @param array  $extra
     */
    private function log(string $message, array $extra = [])
    {
        $this->logger->info('App/Router/NoRouteHandler - ' . $message, $extra);
    }
}
