<?php

declare(strict_types=1);

namespace SR\ProductAttributeOptionSorting\Model;

final class SortMode
{
    public const FIELD = 'sr_option_sorting_mode';
    public const MANUAL = 'manual';
    public const AZ = 'az';

    private const SUPPORTED_INPUT_TYPES = [
        'select',
        'multiselect',
        'swatch_text',
        'swatch_visual',
    ];

    public function isSupportedInputType(string $frontendInput): bool
    {
        return in_array($frontendInput, self::SUPPORTED_INPUT_TYPES, true);
    }

    public function getSupportedInputTypes(): array
    {
        return self::SUPPORTED_INPUT_TYPES;
    }
}
