<?php
/**
 * Copyright (C) EC Brands Corporation - All Rights Reserved
 * Contact Licensing@ECInternet.com for use guidelines
 */
declare(strict_types=1);

namespace ECInternet\CatalogFeatures\Cron;

use ECInternet\CatalogFeatures\Helper\Data;
use ECInternet\CatalogFeatures\Logger\Logger;

/**
 * EmailMissingImageReport Cron
 */
class EmailMissingImageReport
{
    /**
     * @var \ECInternet\CatalogFeatures\Helper\Data
     */
    private $_helper;

    /**
     * @var \ECInternet\CatalogFeatures\Logger\Logger
     */
    private $_logger;

    public function __construct(
        Data $helper,
        Logger $logger
    ) {
        $this->_helper = $helper;
        $this->_logger = $logger;
    }

    /**
     * Execute cron
     */
    public function execute()
    {
        $this->log('execute()');

        $emailRecipients = $this->_helper->getMissingImageReportRecipients();
        if (empty($emailRecipients)) {
            $this->log('execute() - No email recipients');

            return;
        }

        $this->log('execute()', ['emailRecipients' => explode(';', $emailRecipients)]);
    }

    /**
     * Write to extension log
     *
     * @param string $message
     * @param array  $extra
     */
    private function log(string $message, array $extra = [])
    {
        $this->_logger->info('Cron/EmailMissingImageReport - ' . $message, $extra);
    }
}
