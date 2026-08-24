<?php

declare(strict_types=1);

namespace SR\ProductAttributeOptionSorting\Observer;

use Magento\Catalog\Api\Data\ProductAttributeInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use SR\ProductAttributeOptionSorting\Api\OptionSorterInterface;
use SR\ProductAttributeOptionSorting\Model\SortMode;

class SortOptionsAfterAttributeSave implements ObserverInterface
{
    public function __construct(
        private readonly OptionSorterInterface $optionSorter,
        private readonly SortMode $sortMode
    ) {
    }

    public function execute(Observer $observer): void
    {
        $attribute = $observer->getEvent()->getAttribute();
        if (!$attribute instanceof ProductAttributeInterface || !$attribute->getAttributeId()) {
            return;
        }

        $mode = (string)($attribute->getData(SortMode::FIELD) ?: SortMode::MANUAL);
        if ($mode !== SortMode::AZ) {
            return;
        }

        if (!$this->sortMode->isSupportedInputType((string)$attribute->getFrontendInput())) {
            return;
        }

        $this->optionSorter->sort((int)$attribute->getAttributeId(), $mode);
    }
}
