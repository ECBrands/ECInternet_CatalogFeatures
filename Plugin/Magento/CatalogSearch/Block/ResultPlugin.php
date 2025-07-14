<?php
/**
 * Copyright (C) EC Brands Corporation - All Rights Reserved
 * Contact Licensing@ECInternet.com for use guidelines
 */
declare(strict_types=1);

namespace ECInternet\CatalogFeatures\Plugin\Magento\CatalogSearch\Block;

use Magento\CatalogSearch\Block\Result;
use Magento\Framework\Phrase;
use ECInternet\CatalogFeatures\Model\Config;
use Psr\Log\LoggerInterface;

/**
 * Plugin for Magento\CatalogSearch\Block\Result
 */
class ResultPlugin
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
     * ResultPlugin constructor.
     *
     * @param \ECInternet\CatalogFeatures\Model\Config $config
     * @param \Psr\Log\LoggerInterface                 $logger
     */
    public function __construct(
        Config $config,
        LoggerInterface $logger
    ) {
        $this->config = $config;
        $this->logger  = $logger;
    }

    /**
     * Display custom search query text
     *
     * @param \Magento\CatalogSearch\Block\Result $subject
     * @param Phrase                              $result
     *
     * @return Phrase
     */
    public function afterGetSearchQueryText(
        Result $subject,
        Phrase $result
    ) {
        $this->log('afterGetSearchQueryText()', ['result' => $result]);

        if ($this->config->isModuleEnabled()) {
            if ($subject->getRequest()->getParam(Config::URL_PARAM_IS_404_SEARCH)) {
                return __($this->config->getRedirectSearchTitle());
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
        $this->logger->info('Plugin/Magento/CatalogSearch/Block/ResultPlugin - ' . $message, $extra);
    }
}
