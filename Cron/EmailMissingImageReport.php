<?php
/**
 * Copyright (C) EC Brands Corporation - All Rights Reserved
 * Contact Licensing@ECInternet.com for use guidelines
 */
declare(strict_types=1);

namespace ECInternet\CatalogFeatures\Cron;

use ECInternet\CatalogFeatures\Model\Config;
use Psr\Log\LoggerInterface;

/**
 * EmailMissingImageReport Cron
 */
class EmailMissingImageReport
{
    /**
     * @var \ECInternet\CatalogFeatures\Model\Config
     */
    private $config;


    private $logger;

    /**
     * EmailMissingImageReport constructor.
     *
     * @param \ECInternet\CatalogFeatures\Model\Config $config
     * @param \Psr\Log\LoggerInterface                 $logger
     */
    public function __construct(
        Config $config,
        LoggerInterface $logger
    ) {
        $this->config = $config;
        $this->logger = $logger;
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
