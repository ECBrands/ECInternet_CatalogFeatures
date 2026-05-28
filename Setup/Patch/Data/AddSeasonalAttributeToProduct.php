<?php
/**
 * Copyright (C) EC Brands Corporation - All Rights Reserved
 * Contact Licensing@ECInternet.com for use guidelines
 */
declare(strict_types=1);

namespace ECInternet\CatalogFeatures\Setup\Patch\Data;

use Magento\Catalog\Model\Product;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Model\Entity\Attribute\Source\Boolean;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class AddSeasonalAttributeToProduct implements DataPatchInterface
{
    /**
     * @var \Magento\Eav\Setup\EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * @var \Magento\Framework\Setup\ModuleDataSetupInterface
     */
    private $setup;

    public function __construct(
        EavSetupFactory $eavSetupFactory,
        ModuleDataSetupInterface $setup
    ) {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->setup           = $setup;
    }

    public static function getDependencies(): array
    {
        return [AddAllowOnWebAttributeToProduct::class];
    }

    public function getAliases(): array
    {
        return [];
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function apply(): void
    {
        $this->setup->getConnection()->startSetup();

        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->setup]);
        $eavSetup->addAttribute(Product::ENTITY, 'seasonal', [
            'type'                    => 'int',
            'backend'                 => '',
            'frontend'                => '',
            'label'                   => 'Seasonal',
            'input'                   => 'boolean',
            'class'                   => '',
            'source'                  => Boolean::class,
            'global'                  => ScopedAttributeInterface::SCOPE_GLOBAL,
            'visible'                 => true,
            'required'                => false,
            'user_defined'            => false,
            'default'                 => '',
            'searchable'              => false,
            'filterable'              => false,
            'comparable'              => false,
            'visible_on_front'        => false,
            'used_in_product_listing' => false,
            'unique'                  => false,
            'apply_to'                => '',
        ]);

        $this->setup->getConnection()->endSetup();
    }
}
