<?php
/**
 * Copyright (C) EC Brands Corporation - All Rights Reserved
 * Contact Licensing@ECInternet.com for use guidelines
 */
declare(strict_types=1);

namespace ECInternet\CatalogFeatures\Plugin\Magento\CatalogSearch\Block;

use Magento\CatalogSearch\Block\Result;
use Magento\Framework\Phrase;
use ECInternet\CatalogFeatures\Helper\Data;
use ECInternet\CatalogFeatures\Logger\Logger;

/**
 * Plugin for Magento\CatalogSearch\Block\Result
 */
class ResultPlugin
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

        if ($this->_helper->isModuleEnabled()) {
            if ($subject->getRequest()->getParam(Data::URL_PARAM_IS_404_SEARCH)) {
                return __($this->_helper->getRedirectSearchTitle());
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
        $this->_logger->info('Plugin/Magento/CatalogSearch/Block/ResultPlugin - ' . $message, $extra);
    }
}
