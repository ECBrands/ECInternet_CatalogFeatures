<?php
/**
 * Copyright (C) EC Brands Corporation - All Rights Reserved
 * Contact Licensing@ECInternet.com for use guidelines
 */
declare(strict_types=1);

namespace ECInternet\CatalogFeatures\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use ECInternet\CatalogFeatures\Helper\Data;

/**
 * Observer for 'catalog_category_collection_load_after' event
 */
class CatalogCategoryCollectionLoadAfter implements ObserverInterface
{
    /**
     * @var \ECInternet\CatalogFeatures\Helper\Data
     */
    private $_helper;

    /**
     * CatalogCategoryCollectionLoadAfter constructor.
     *
     * @param \ECInternet\CatalogFeatures\Helper\Data $helper
     */
    public function __construct(
        Data $helper
    ) {
        $this->_helper = $helper;
    }

    /**
     * Remove categories from collection which have empty product collections
     *
     * @param \Magento\Framework\Event\Observer $observer
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(
        EventObserver $observer
    ) {
        if ($this->_helper->isModuleEnabled() && $this->_helper->hideEmptyCategories()) {
            /** @var \Magento\Catalog\Model\ResourceModel\Category\Collection $filteredCategoryCollection */
            $filteredCategoryCollection = $observer->getData('category_collection');

            /** @var \Magento\Catalog\Model\ResourceModel\Category\Collection $originalCategoryCollection */
            $originalCategoryCollection = clone $filteredCategoryCollection;

            // Remove all items from the filtered one, we'll re-add the ones that aren't empty.
            $filteredCategoryCollection->removeAllItems();

            /** @var \Magento\Catalog\Model\Category $category */
            foreach ($originalCategoryCollection as $category) {
                if ($category->getProductCollection()->getSize()) {
                    // Category isn't empty, re-add it.
                    $filteredCategoryCollection->addItem($category);
                }
            }
        }
    }
}
