<?php
/**
 * Copyright (C) EC Brands Corporation - All Rights Reserved
 * Contact Licensing@ECInternet.com for use guidelines
 */
declare(strict_types=1);

namespace ECInternet\CatalogFeatures\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use ECInternet\CatalogFeatures\Model\Config;

/**
 * Observer for 'catalog_category_collection_load_after' event
 */
class CatalogCategoryCollectionLoadAfter implements ObserverInterface
{
    /**
     * @var \ECInternet\CatalogFeatures\Model\Config
     */
    private $config;

    /**
     * CatalogCategoryCollectionLoadAfter constructor.
     *
     * @param \ECInternet\CatalogFeatures\Model\Config $config
     */
    public function __construct(
        Config $config
    ) {
        $this->config = $config;
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
        if ($this->config->isModuleEnabled() && $this->config->hideEmptyCategories()) {
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
