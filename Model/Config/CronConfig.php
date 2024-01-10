<?php
/**
 * Copyright (C) EC Brands Corporation - All Rights Reserved
 * Contact Licensing@ECInternet.com for use guidelines
 */
declare(strict_types=1);

namespace ECInternet\CatalogFeatures\Model\Config;

use Magento\Cron\Model\Config\Source\Frequency;
use Magento\Cron\Model\Schedule;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Value as ConfigValue;
use Magento\Framework\App\Config\ValueFactory;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use ECInternet\CatalogFeatures\Logger\Logger;
use Exception;

class CronConfig extends ConfigValue
{
    /**
     * Cron string path
     */
    const CRON_STRING_PATH = 'crontab/default/jobs/ecinternet_catalogfeatures_emailmissingimagereport_cronjob/schedule/cron_expr';

    /**
     * Cron model path
     */
    const CRON_MODEL_PATH = 'crontab/default/jobs/ecinternet_catalogfeatures_emailmissingimagereport_cronjob/run/model';

    /**
     * @var \Magento\Cron\Model\Schedule
     */
    protected $_schedule;

    /**
     * @var \Magento\Framework\App\Config\ValueFactory
     */
    protected $_configValueFactory;

    /**
     * @var string
     */
    protected $_runModelPath = '';

    /**
     * @var \ECInternet\CatalogFeatures\Logger\Logger
     */
    private $logger;

    /**
     * @param \Magento\Framework\Model\Context                             $context
     * @param \Magento\Framework\Registry                                  $registry
     * @param \Magento\Framework\App\Config\ScopeConfigInterface           $config
     * @param \Magento\Framework\App\Cache\TypeListInterface               $cacheTypeList
     * @param \Magento\Framework\App\Config\ValueFactory                   $configValueFactory
     * @param \Magento\Cron\Model\Schedule                                 $schedule
     * @param \ECInternet\CatalogFeatures\Logger\Logger                    $logger
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null           $resourceCollection
     * @param string                                                       $runModelPath
     * @param array                                                        $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        ScopeConfigInterface $config,
        TypeListInterface $cacheTypeList,
        ValueFactory $configValueFactory,
        Schedule $schedule,
        Logger $logger,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        string $runModelPath = '',
        array $data = []
    ) {
        $this->_runModelPath       = $runModelPath;
        $this->_configValueFactory = $configValueFactory;
        $this->_schedule           = $schedule;
        $this->logger              = $logger;

        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);
    }

    /**
     * Create config setting for cron frequency
     *
     * @return \ECInternet\CatalogFeatures\Model\Config\CronConfig
     * @throws \Exception
     */
    public function afterSave()
    {
        $this->log('afterSave()');

        $time = $this->getData('groups/missing_image_report/fields/time/value');
        $this->log('afterSave()', ['time' => $time]);

        $frequency = $this->getData('groups/missing_image_report/fields/frequency/value');
        $this->log('afterSave()', ['frequency' => $frequency]);

        $dayOfWeek = $this->getData('groups/missing_image_report/fields/dayofweek/value');
        $this->log('afterSave()', ['dayOfWeek' => $dayOfWeek]);

        $dayOfWeekValue = $this->getDayOfWeekValue($frequency, $dayOfWeek);
        $this->log('afterSave()', ['dayOfWeekValue' => $dayOfWeekValue]);

        $cronExprArray = [
            (int)$time[1], // Minute
            (int)$time[0], // Hour
            $frequency == Frequency::CRON_MONTHLY ? '1' : '*', // Day of the month
            '*', // Month of the year
            $dayOfWeekValue, // Day of the week
        ];

        $cronExprString = join(' ', $cronExprArray);

        try {
            $this->_configValueFactory->create()->load(
                self::CRON_STRING_PATH,
                'path'
            )->setValue(
                $cronExprString
            )->setPath(
                self::CRON_STRING_PATH
            )->save();

            $this->_configValueFactory->create()->load(
                self::CRON_MODEL_PATH,
                'path'
            )->setValue(
                $this->_runModelPath
            )->setPath(
                self::CRON_MODEL_PATH
            )->save();

            $this->log('afterSave() - Complete');
        } catch (Exception $e) {
            throw new Exception("We can't save the cron expression.");
        }

        return parent::afterSave();
    }

    private function getDayOfWeekValue($frequency, $dayOfWeek)
    {
        $this->log('getDayOfWeekValue()', ['frequency' => $frequency, 'dayOfWeek' => $dayOfWeek]);

        $value = '*';

        if ($frequency == Frequency::CRON_WEEKLY) {
            $numeric = $this->getNumeric($dayOfWeek);
            $this->log('getDayOfWeekValue()', ['numeric' => $numeric]);

            if ($numeric !== false) {
                $value = $numeric;
            }
        }

        return $value;
    }

    private function getNumeric($value)
    {
        return $this->_schedule->getNumeric($value);
    }

    private function log(string $message, array $extra = [])
    {
        $this->logger->info('Model/Config/CronConfig - ' . $message, $extra);
    }
}
