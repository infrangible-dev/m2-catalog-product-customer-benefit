<?php

declare(strict_types=1);

namespace Infrangible\CatalogProductCustomerBenefit\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * @throws \Exception
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $connection = $setup->getConnection();

        $customerBenefitTableName = $setup->getTable('catalog_product_customer_benefit');

        if (! $setup->tableExists($customerBenefitTableName)) {
            $productEntityTableName = $setup->getTable('catalog_product_entity');

            $customerBenefitTable = $connection->newTable($customerBenefitTableName);

            $customerBenefitTable->addColumn(
                'id',
                Table::TYPE_INTEGER,
                10,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true]
            );
            $customerBenefitTable->addColumn(
                'source_product_id',
                Table::TYPE_INTEGER,
                10,
                ['unsigned' => true, 'nullable' => false]
            );
            $customerBenefitTable->addColumn(
                'source_product_option_value_id',
                Table::TYPE_INTEGER,
                10,
                ['unsigned' => true, 'nullable' => true]
            );
            $customerBenefitTable->addColumn(
                'target_product_id',
                Table::TYPE_INTEGER,
                10,
                ['unsigned' => true, 'nullable' => false]
            );
            $customerBenefitTable->addColumn(
                'price',
                Table::TYPE_DECIMAL,
                [20, 2],
                ['unsigned' => false, 'nullable' => true]
            );
            $customerBenefitTable->addColumn(
                'discount',
                Table::TYPE_SMALLINT,
                1,
                ['unsigned' => true, 'nullable' => true]
            );
            $customerBenefitTable->addColumn(
                'limit',
                Table::TYPE_SMALLINT,
                5,
                ['unsigned' => true, 'nullable' => true]
            );
            $customerBenefitTable->addColumn(
                'created_at_days_before',
                Table::TYPE_SMALLINT,
                5,
                ['unsigned' => true, 'nullable' => true]
            );
            $customerBenefitTable->addColumn(
                'api_flag',
                Table::TYPE_TEXT,
                2555,
                ['nullable' => true]
            );
            $customerBenefitTable->addColumn(
                'priority',
                Table::TYPE_SMALLINT,
                5,
                ['unsigned' => true, 'nullable' => false, 'default' => '100']
            );
            $customerBenefitTable->addColumn(
                'active',
                Table::TYPE_SMALLINT,
                1,
                ['nullable' => false, 'unsigned' => true, 'default' => 0]
            );
            $customerBenefitTable->addColumn(
                'created_at',
                Table::TYPE_DATETIME,
                null,
                ['nullable' => false, 'default' => '0000-00-00 00:00:00']
            );
            $customerBenefitTable->addColumn(
                'updated_at',
                Table::TYPE_DATETIME,
                null,
                ['nullable' => false, 'default' => '0000-00-00 00:00:00']
            );

            $customerBenefitTable->addForeignKey(
                $setup->getFkName(
                    $customerBenefitTableName,
                    'source_product_id',
                    $productEntityTableName,
                    'entity_id'
                ),
                'source_product_id',
                $productEntityTableName,
                'entity_id',
                Table::ACTION_CASCADE
            );

            $customerBenefitTable->addForeignKey(
                $setup->getFkName(
                    $customerBenefitTableName,
                    'target_product_id',
                    $productEntityTableName,
                    'entity_id'
                ),
                'target_product_id',
                $productEntityTableName,
                'entity_id',
                Table::ACTION_CASCADE
            );

            $customerBenefitTable->addIndex(
                $setup->getIdxName(
                    $customerBenefitTableName,
                    ['source_product_id']
                ),
                ['source_product_id']
            );

            $connection->createTable($customerBenefitTable);
        }

        $setup->endSetup();
    }
}
