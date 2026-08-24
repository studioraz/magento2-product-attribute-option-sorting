<?php

declare(strict_types=1);

namespace SR\ProductAttributeOptionSorting\Service;

use Magento\Eav\Model\Config as EavConfig;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Locale\ResolverInterface;
use SR\ProductAttributeOptionSorting\Api\OptionSorterInterface;
use SR\ProductAttributeOptionSorting\Model\SortMode;

class OptionSorter implements OptionSorterInterface
{
    public function __construct(
        private readonly ResourceConnection $resourceConnection,
        private readonly ResolverInterface $localeResolver,
        private readonly EavConfig $eavConfig
    ) {
    }

    public function sort(int $attributeId, string $mode): int
    {
        if ($attributeId <= 0 || $mode !== SortMode::AZ) {
            return 0;
        }

        $connection = $this->resourceConnection->getConnection();
        $options = $this->getOptions($connection, $attributeId);
        if (count($options) < 2) {
            return 0;
        }

        usort($options, fn (array $left, array $right): int => $this->compareOptions($left, $right));

        $updated = $this->updateSortOrder($connection, $options);
        if ($updated > 0) {
            $this->eavConfig->clear();
        }

        return $updated;
    }

    private function getOptions(AdapterInterface $connection, int $attributeId): array
    {
        $optionTable = $this->resourceConnection->getTableName('eav_attribute_option');
        $valueTable = $this->resourceConnection->getTableName('eav_attribute_option_value');

        $select = $connection->select()
            ->from(
                ['option' => $optionTable],
                ['option_id', 'sort_order']
            )
            ->joinLeft(
                ['value' => $valueTable],
                'value.option_id = option.option_id AND value.store_id = 0',
                ['value']
            )
            ->where('option.attribute_id = ?', $attributeId)
            ->order('option.sort_order ASC')
            ->order('option.option_id ASC');

        return $connection->fetchAll($select);
    }

    private function compareOptions(array $left, array $right): int
    {
        $leftLabel = trim((string)($left['value'] ?? ''));
        $rightLabel = trim((string)($right['value'] ?? ''));

        $comparison = $this->compareLabels($leftLabel, $rightLabel);
        if ($comparison !== 0) {
            return $comparison;
        }

        $comparison = (int)$left['sort_order'] <=> (int)$right['sort_order'];
        if ($comparison !== 0) {
            return $comparison;
        }

        return (int)$left['option_id'] <=> (int)$right['option_id'];
    }

    private function compareLabels(string $left, string $right): int
    {
        if (class_exists(\Collator::class)) {
            $collator = new \Collator($this->localeResolver->getLocale());
            $collator->setStrength(\Collator::SECONDARY);

            return $collator->compare($left, $right) ?: 0;
        }

        return strnatcasecmp($left, $right);
    }

    private function updateSortOrder(AdapterInterface $connection, array $options): int
    {
        $optionTable = $this->resourceConnection->getTableName('eav_attribute_option');
        $updated = 0;

        $connection->beginTransaction();
        try {
            foreach ($options as $sortOrder => $option) {
                if ((int)$option['sort_order'] === $sortOrder) {
                    continue;
                }

                $connection->update(
                    $optionTable,
                    ['sort_order' => $sortOrder],
                    ['option_id = ?' => (int)$option['option_id']]
                );
                $updated++;
            }
            $connection->commit();
        } catch (\Throwable $exception) {
            $connection->rollBack();
            throw $exception;
        }

        return $updated;
    }
}
