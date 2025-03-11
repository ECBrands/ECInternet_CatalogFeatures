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
     * ResultPlugin constructor.
     *
     * @param \ECInternet\CatalogFeatures\Model\Config $config
     */
    public function __construct(
        Config $config
    ) {
        $this->config = $config;
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
        if ($this->config->isModuleEnabled()) {
            if ($subject->getRequest()->getParam(Config::URL_PARAM_IS_404_SEARCH)) {
                return __($this->config->getRedirectSearchTitle());
            }
        }

        return $result;
    }
}
