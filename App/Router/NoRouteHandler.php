<?php
/**
 * Copyright (C) EC Brands Corporation - All Rights Reserved
 * Contact Licensing@ECInternet.com for use guidelines
 */
declare(strict_types=1);

namespace ECInternet\CatalogFeatures\App\Router;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Filesystem\Io\File;
use Magento\Framework\Http\PhpEnvironment\Request as HttpRequest;
use ECInternet\CatalogFeatures\Model\Config;
use Exception;
use Psr\Log\LoggerInterface;

/**
 * Handler for NoRoute
 */
class NoRouteHandler extends \Magento\Framework\App\Router\NoRouteHandler
{
    /**
     * @var \Magento\Framework\Filesystem\Io\File
     */
    private $file;

    /**
     * @var \ECInternet\CatalogFeatures\Model\Config
     */
    private $config;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * NoRouteHandler constructor.
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Filesystem\Io\File              $file
     * @param \ECInternet\CatalogFeatures\Model\Config           $config
     * @param \Psr\Log\LoggerInterface                           $logger
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        File $file,
        Config $config,
        LoggerInterface $logger
    ) {
        parent::__construct($scopeConfig);

        $this->file   = $file;
        $this->config = $config;
        $this->logger = $logger;
    }

    /**
     * @inheritDoc
     */
    public function process(
        RequestInterface $request
    ) {
        $this->log('process()');

        if ($request instanceof HttpRequest) {
            $this->log('process()', ['pathInfo' => $request->getPathInfo()]);

            try {
                // Check if this is a product or category page and redirect to search instead.
                if ($this->shouldRedirectToSearch()) {
                    $requestValue = $this->baseName($request->getPathInfo());
                    $this->log('process()', ['requestValue' => $requestValue]);

                    if (strpos($requestValue, '.html') !== false) {
                        $productName = str_replace('-', ' ', str_replace('.html', '', urldecode($requestValue)));
                        $this->log('process()', ['productName' => $productName]);

                        if (!empty($productName)) {
                            $request->setParams(['q' => $productName, Config::URL_PARAM_IS_404_SEARCH => true]);
                            $request->setModuleName('catalogsearch')->setControllerName('result')->setActionName('index');

                            return true;
                        }
                    }
                }
            } catch (Exception $e) {
                $this->log('process()', ['exception' => $e->getMessage()]);
            }
        }

        // Stock behavior.
        return parent::process($request);
    }

    /**
     * Should we redirect to the search page when a user hits a 404?
     *
     * @return bool
     */
    private function shouldRedirectToSearch()
    {
        return $this->config->isModuleEnabled() && $this->config->shouldRedirectToSearchFor404Pages();
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
