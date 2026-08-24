<?php

declare(strict_types=1);

namespace SR\ProductAttributeOptionSorting\Test\Unit\Observer;

use Magento\Catalog\Model\ResourceModel\Eav\Attribute as CatalogAttribute;
use Magento\Framework\DataObject;
use Magento\Framework\Event\Observer;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use SR\ProductAttributeOptionSorting\Api\OptionSorterInterface;
use SR\ProductAttributeOptionSorting\Model\SortMode;
use SR\ProductAttributeOptionSorting\Observer\SortOptionsAfterAttributeSave;

class SortOptionsAfterAttributeSaveTest extends TestCase
{
    private OptionSorterInterface&MockObject $optionSorter;

    private SortOptionsAfterAttributeSave $observer;

    protected function setUp(): void
    {
        $this->optionSorter = $this->createMock(OptionSorterInterface::class);
        $this->observer = new SortOptionsAfterAttributeSave(
            $this->optionSorter,
            new SortMode()
        );
    }

    public function testSkipsNonProductAttributes(): void
    {
        $this->optionSorter->expects($this->never())->method('sort');

        $this->observer->execute($this->createObserver(new DataObject()));
    }

    public function testSkipsManualMode(): void
    {
        $attribute = $this->createAttribute(10, SortMode::MANUAL, 'select');

        $this->optionSorter->expects($this->never())->method('sort');

        $this->observer->execute($this->createObserver($attribute));
    }

    public function testSkipsUnsupportedFrontendInput(): void
    {
        $attribute = $this->createAttribute(10, SortMode::AZ, 'text');

        $this->optionSorter->expects($this->never())->method('sort');

        $this->observer->execute($this->createObserver($attribute));
    }

    public function testSortsSupportedAzProductAttribute(): void
    {
        $attribute = $this->createAttribute(10, SortMode::AZ, 'swatch_visual');

        $this->optionSorter->expects($this->once())
            ->method('sort')
            ->with(10, SortMode::AZ);

        $this->observer->execute($this->createObserver($attribute));
    }

    private function createObserver(object $attribute): Observer
    {
        return new Observer([
            'event' => new DataObject([
                'attribute' => $attribute,
            ]),
        ]);
    }

    /**
     * @return CatalogAttribute&MockObject
     */
    private function createAttribute(int $attributeId, string $mode, string $frontendInput): CatalogAttribute
    {
        $attribute = $this->getMockBuilder(CatalogAttribute::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getAttributeId', 'getData', 'getFrontendInput'])
            ->getMock();
        $attribute->method('getAttributeId')->willReturn($attributeId);
        $attribute->method('getData')->with(SortMode::FIELD)->willReturn($mode);
        $attribute->method('getFrontendInput')->willReturn($frontendInput);

        return $attribute;
    }
}
