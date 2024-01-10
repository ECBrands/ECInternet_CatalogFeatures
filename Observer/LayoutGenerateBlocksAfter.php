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

use ECInternet\CatalogFeatures\Helper\Data;

/**
 * Observer for 'layout_generate_blocks_after' event
 */
class LayoutGenerateBlocksAfter implements ObserverInterface
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    private $_customerSession;

    /**
     * @var \ECInternet\CatalogFeatures\Helper\Data
     */
    private $_helper;

    /**
     * LayoutGenerateBlocksAfter constructor.
     *
     * @param \Magento\Customer\Model\Session         $customerSession
     * @param \ECInternet\CatalogFeatures\Helper\Data $helper
     */
    public function __construct(
        CustomerSession $customerSession,
        Data $helper
    ) {
        $this->_customerSession = $customerSession;
        $this->_helper          = $helper;
    }

    /**
     * Remove price blocks for guests
     *
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(
        EventObserver $observer
    ) {
        if ($this->_helper->isModuleEnabled()) {
            if (!$this->_customerSession->isLoggedIn()) {
                if ($this->_helper->hidePricesForGuests()) {
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
