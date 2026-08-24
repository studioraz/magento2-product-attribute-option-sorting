<?php

declare(strict_types=1);

namespace SR\ProductAttributeOptionSorting\Api;

interface OptionSorterInterface
{
    /**
     * Sort attribute options according to the requested mode.
     *
     * @return int Number of option rows updated.
     */
    public function sort(int $attributeId, string $mode): int;
}
