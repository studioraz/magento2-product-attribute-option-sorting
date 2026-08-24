<?php

declare(strict_types=1);

namespace SR\ProductAttributeOptionSorting\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;
use SR\ProductAttributeOptionSorting\Model\SortMode as SortModeValue;

class SortMode implements OptionSourceInterface
{
    public function toOptionArray(): array
    {
        return [
            [
                'value' => SortModeValue::MANUAL,
                'label' => __('Manually'),
            ],
            [
                'value' => SortModeValue::AZ,
                'label' => __('A to Z'),
            ],
        ];
    }
}
