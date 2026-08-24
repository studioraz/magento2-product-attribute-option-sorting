<?php

declare(strict_types=1);

namespace SR\ProductAttributeOptionSorting\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Registry;
use SR\ProductAttributeOptionSorting\Model\SortMode;
use SR\ProductAttributeOptionSorting\Model\Source\SortMode as SortModeSource;

class AddSortModeField implements ObserverInterface
{
    public function __construct(
        private readonly SortModeSource $sortModeSource,
        private readonly Registry $registry
    ) {
    }

    public function execute(Observer $observer): void
    {
        $form = $observer->getEvent()->getForm();
        if (!$form) {
            return;
        }

        $fieldset = $form->getElement('base_fieldset');
        if (!$fieldset || $form->getElement(SortMode::FIELD)) {
            return;
        }

        $attribute = $this->registry->registry('entity_attribute');
        $value = $attribute ? (string)$attribute->getData(SortMode::FIELD) : '';

        $fieldset->addField(
            SortMode::FIELD,
            'select',
            [
                'name' => SortMode::FIELD,
                'label' => __('Sort options by'),
                'title' => __('Sort options by'),
                'value' => $value ?: SortMode::MANUAL,
                'values' => $this->sortModeSource->toOptionArray(),
            ],
            'is_required'
        );
    }
}
