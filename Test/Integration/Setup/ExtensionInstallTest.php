<?php
/**
 * Copyright (C) EC Brands Corporation - All Rights Reserved
 * Contact Licensing@ECInternet.com for use guidelines
 */
declare(strict_types=1);

namespace ECInternet\CatalogFeatures\Test\Integration\Setup;

use Magento\Eav\Model\Config as EavConfig;
use Magento\TestFramework\Helper\Bootstrap;
use Exception;
use PHPUnit\Framework\TestCase;

class ExtensionInstallTest extends TestCase
{
    /**
     * @var \Magento\Eav\Model\Config
     */
    private $eavConfig;

    protected function setUp(): void
    {
        $objectManager   = Bootstrap::getObjectManager();
        $this->eavConfig = $objectManager->get(EavConfig::class);
    }

    // -------------------------------------------------------------------------
    // Product EAV attributes
    // -------------------------------------------------------------------------

    public function testProductAttributeAllowOnWebWasCreatedCorrectly(): void
    {
        /** @var \Magento\Eav\Model\Entity\Attribute\AbstractAttribute $attribute */
        $attribute = $this->getAttribute('catalog_product', 'allow_on_web');

        $this->assertNotNull($attribute, 'Product attribute "allow_on_web" does not exist.');
        $this->assertEquals('int', $attribute->getBackendType());
        $this->assertEquals('Allow On Web', $attribute->getStoreLabel());
        $this->assertEquals('boolean', $attribute->getFrontendInput());
        $this->assertEquals(0, $attribute->getIsRequired());
        $this->assertEquals(1, $attribute->getData('is_visible'));
        $this->assertEquals(0, $attribute->getIsUserDefined());
        $this->assertEquals(0, $attribute->getIsUnique());
    }

    public function testProductAttributeSeasonalWasCreatedCorrectly(): void
    {
        /** @var \Magento\Eav\Model\Entity\Attribute\AbstractAttribute $attribute */
        $attribute = $this->getAttribute('catalog_product', 'seasonal');

        $this->assertNotNull($attribute, 'Product attribute "seasonal" does not exist.');
        $this->assertEquals('int', $attribute->getBackendType());
        $this->assertEquals('Seasonal', $attribute->getStoreLabel());
        $this->assertEquals('boolean', $attribute->getFrontendInput());
        $this->assertEquals(0, $attribute->getIsRequired());
        $this->assertEquals(1, $attribute->getData('is_visible'));
        $this->assertEquals(0, $attribute->getIsUserDefined());
        $this->assertEquals(0, $attribute->getIsUnique());
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    private function getAttribute(string $entityTypeCode, string $attributeCode)
    {
        try {
            if ($attribute = $this->eavConfig->getAttribute($entityTypeCode, $attributeCode)) {
                if ($attribute->getAttributeId()) {
                    return $attribute;
                }
            }
        } catch (Exception) {
        }

        return null;
    }
}
