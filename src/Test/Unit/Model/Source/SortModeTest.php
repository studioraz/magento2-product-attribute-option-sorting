<?php

declare(strict_types=1);

namespace SR\ProductAttributeOptionSorting\Test\Unit\Model\Source;

use PHPUnit\Framework\TestCase;
use SR\ProductAttributeOptionSorting\Model\SortMode as SortModeValue;
use SR\ProductAttributeOptionSorting\Model\Source\SortMode;

class SortModeTest extends TestCase
{
    public function testReturnsSortingModeOptions(): void
    {
        $source = new SortMode();
        $options = $source->toOptionArray();

        $this->assertCount(2, $options);
        $this->assertSame(SortModeValue::MANUAL, $options[0]['value']);
        $this->assertSame('Manually', (string)$options[0]['label']);
        $this->assertSame(SortModeValue::AZ, $options[1]['value']);
        $this->assertSame('A to Z', (string)$options[1]['label']);
    }
}
