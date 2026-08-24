<?php

declare(strict_types=1);

namespace SR\ProductAttributeOptionSorting\Test\Unit\Model;

use PHPUnit\Framework\TestCase;
use SR\ProductAttributeOptionSorting\Model\SortMode;

class SortModeTest extends TestCase
{
    public function testDetectsSupportedInputTypes(): void
    {
        $sortMode = new SortMode();

        $this->assertTrue($sortMode->isSupportedInputType('select'));
        $this->assertTrue($sortMode->isSupportedInputType('multiselect'));
        $this->assertTrue($sortMode->isSupportedInputType('swatch_text'));
        $this->assertTrue($sortMode->isSupportedInputType('swatch_visual'));
    }

    public function testRejectsUnsupportedInputTypes(): void
    {
        $sortMode = new SortMode();

        $this->assertFalse($sortMode->isSupportedInputType('text'));
        $this->assertFalse($sortMode->isSupportedInputType('textarea'));
        $this->assertFalse($sortMode->isSupportedInputType('boolean'));
    }
}
