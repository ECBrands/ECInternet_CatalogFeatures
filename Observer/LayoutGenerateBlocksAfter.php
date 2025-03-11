<?php
/**
 * Copyright (C) EC Brands Corporation - All Rights Reserved
 * Contact Licensing@ECInternet.com for use guidelines
 */
declare(strict_types=1);

namespace ECInternet\CatalogFeatures\Observer;

use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\View\LayoutInterface;
use ECInternet\CatalogFeatures\Model\Config;

/**
 * Observer for 'layout_generate_blocks_after' event
 */
class LayoutGenerateBlocksAfter implements ObserverInterface
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    private $customerSession;

    /**
     * @var \ECInternet\CatalogFeatures\Model\Config
     */
    private $config;

    /**
     * LayoutGenerateBlocksAfter constructor.
     *
     * @param \Magento\Customer\Model\Session          $customerSession
     * @param \ECInternet\CatalogFeatures\Model\Config $config
     */
    public function __construct(
        CustomerSession $customerSession,
        Config $config
    ) {
        $this->customerSession = $customerSession;
        $this->config          = $config;
    }

    /**
     * Remove price blocks for guests
     *
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(
        EventObserver $observer
    ) {
        if ($this->config->isModuleEnabled()) {
            if (!$this->customerSession->isLoggedIn()) {
                if ($this->config->hidePricesForGuests()) {
                    /** @var \Magento\Framework\View\Layout $layout */
                    $layout = $observer->getData('layout');
                    if (isset($layout)) {
                        $this->removeBlock($layout, 'product.price.final');
                        $this->removeBlock($layout, 'product.info.addtocart');
                        $this->removeBlock($layout, 'category.product.type.details.renderers');
                    }
                }
            }
        }
    }

    /**
     * Remove block from layout if it exists.
     *
     * @param \Magento\Framework\View\LayoutInterface $layout
     * @param string                                  $blockName
     */
    private function removeBlock(
        LayoutInterface $layout,
        string $blockName
    ) {
        if ($layout->getBlock($blockName)) {
            $layout->unsetElement($blockName);
        }
    }
}
