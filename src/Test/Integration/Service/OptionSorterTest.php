<?php

declare(strict_types=1);

namespace SR\ProductAttributeOptionSorting\Test\Integration\Service;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;
use SR\ProductAttributeOptionSorting\Api\OptionSorterInterface;
use SR\ProductAttributeOptionSorting\Model\SortMode;

class OptionSorterTest extends TestCase
{
    private AdapterInterface $connection;

    private OptionSorterInterface $optionSorter;

    private ResourceConnection $resourceConnection;

    protected function setUp(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        $this->resourceConnection = $objectManager->get(ResourceConnection::class);
        $this->connection = $this->resourceConnection->getConnection();
        $this->optionSorter = $objectManager->get(OptionSorterInterface::class);
    }

    /**
     * @magentoDbIsolation enabled
     */
    public function testSortsOptionsByDefaultLabel(): void
    {
        $attributeId = $this->createAttribute('sr_sort_test_' . uniqid());
        $this->createOption($attributeId, 'Gamma', 0);
        $this->createOption($attributeId, 'Alpha', 1);
        $this->createOption($attributeId, 'Beta', 2);

        $this->assertSame(3, $this->optionSorter->sort($attributeId, SortMode::AZ));

        $this->assertSame(
            [
                ['value' => 'Alpha', 'sort_order' => '0'],
                ['value' => 'Beta', 'sort_order' => '1'],
                ['value' => 'Gamma', 'sort_order' => '2'],
            ],
            $this->getOptionRows($attributeId)
        );
    }

    /**
     * @magentoDbIsolation enabled
     */
    public function testDuplicateLabelsPreservePreviousRelativeOrder(): void
    {
        $attributeId = $this->createAttribute('sr_sort_test_' . uniqid());
        $betaOptionId = $this->createOption($attributeId, 'Beta', 10);
        $firstAlphaOptionId = $this->createOption($attributeId, 'Alpha', 20);
        $secondAlphaOptionId = $this->createOption($attributeId, 'Alpha', 30);

        $this->optionSorter->sort($attributeId, SortMode::AZ);

        $this->assertSame(
            [
                ['option_id' => (string)$firstAlphaOptionId, 'value' => 'Alpha', 'sort_order' => '0'],
                ['option_id' => (string)$secondAlphaOptionId, 'value' => 'Alpha', 'sort_order' => '1'],
                ['option_id' => (string)$betaOptionId, 'value' => 'Beta', 'sort_order' => '2'],
            ],
            $this->getOptionRows($attributeId, ['option_id', 'value', 'sort_order'])
        );
    }

    /**
     * @magentoDbIsolation enabled
     */
    public function testManualModeLeavesSortOrderUnchanged(): void
    {
        $attributeId = $this->createAttribute('sr_sort_test_' . uniqid());
        $this->createOption($attributeId, 'Gamma', 10);
        $this->createOption($attributeId, 'Alpha', 20);
        $this->createOption($attributeId, 'Beta', 30);

        $this->assertSame(0, $this->optionSorter->sort($attributeId, SortMode::MANUAL));

        $this->assertSame(
            [
                ['value' => 'Gamma', 'sort_order' => '10'],
                ['value' => 'Alpha', 'sort_order' => '20'],
                ['value' => 'Beta', 'sort_order' => '30'],
            ],
            $this->getOptionRows($attributeId)
        );
    }

    private function createAttribute(string $attributeCode): int
    {
        $entityTypeTable = $this->resourceConnection->getTableName('eav_entity_type');
        $attributeTable = $this->resourceConnection->getTableName('eav_attribute');

        $entityTypeId = (int)$this->connection->fetchOne(
            $this->connection->select()
                ->from($entityTypeTable, 'entity_type_id')
                ->where('entity_type_code = ?', 'catalog_product')
        );

        $this->connection->insert(
            $attributeTable,
            [
                'entity_type_id' => $entityTypeId,
                'attribute_code' => $attributeCode,
                'backend_type' => 'int',
                'frontend_input' => 'select',
                'frontend_label' => 'SR Sort Test',
                'is_required' => 0,
                'is_user_defined' => 1,
                'is_unique' => 0,
            ]
        );

        return (int)$this->connection->lastInsertId($attributeTable);
    }

    private function createOption(int $attributeId, string $label, int $sortOrder): int
    {
        $optionTable = $this->resourceConnection->getTableName('eav_attribute_option');
        $valueTable = $this->resourceConnection->getTableName('eav_attribute_option_value');

        $this->connection->insert(
            $optionTable,
            [
                'attribute_id' => $attributeId,
                'sort_order' => $sortOrder,
            ]
        );
        $optionId = (int)$this->connection->lastInsertId($optionTable);

        $this->connection->insert(
            $valueTable,
            [
                'option_id' => $optionId,
                'store_id' => 0,
                'value' => $label,
            ]
        );

        return $optionId;
    }

    private function getOptionRows(int $attributeId, array $columns = ['value', 'sort_order']): array
    {
        $optionTable = $this->resourceConnection->getTableName('eav_attribute_option');
        $valueTable = $this->resourceConnection->getTableName('eav_attribute_option_value');

        $select = $this->connection->select()
            ->from(['option' => $optionTable], array_intersect($columns, ['option_id', 'sort_order']))
            ->joinInner(
                ['value' => $valueTable],
                'value.option_id = option.option_id AND value.store_id = 0',
                array_intersect($columns, ['value'])
            )
            ->where('option.attribute_id = ?', $attributeId)
            ->order('option.sort_order ASC')
            ->order('option.option_id ASC');

        return array_map(
            static function (array $row) use ($columns): array {
                $normalizedRow = [];
                foreach ($columns as $column) {
                    $normalizedRow[$column] = (string)$row[$column];
                }

                return $normalizedRow;
            },
            $this->connection->fetchAll($select)
        );
    }
}
