<?php
/**
 * Copyright (C) EC Brands Corporation - All Rights Reserved
 * Contact Licensing@ECInternet.com for use guidelines
 */
declare(strict_types=1);

namespace ECInternet\CatalogFeatures\Cron;

use ECInternet\CatalogFeatures\Logger\Logger;
use ECInternet\CatalogFeatures\Model\Config;

/**
 * EmailMissingImageReport Cron
 */
class EmailMissingImageReport
{
    /**
     * @var \ECInternet\CatalogFeatures\Logger\Logger
     */
    private $logger;

    /**
     * @var \ECInternet\CatalogFeatures\Model\Config
     */
    private $config;

    /**
     * EmailMissingImageReport constructor.
     *
     * @param \ECInternet\CatalogFeatures\Logger\Logger $logger
     * @param \ECInternet\CatalogFeatures\Model\Config  $config
     */
    public function __construct(
        Logger $logger,
        Config $config
    ) {
        $this->logger = $logger;
        $this->config = $config;
    }

    /**
     * Execute cron
     */
    public function execute()
    {
        $this->log('execute()');

        $emailRecipients = $this->config->getMissingImageReportRecipients();
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
        $this->logger->info('Cron/EmailMissingImageReport - ' . $message, $extra);
    }
}
