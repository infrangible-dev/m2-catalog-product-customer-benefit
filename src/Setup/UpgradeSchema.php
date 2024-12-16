<?php

declare(strict_types=1);

namespace Infrangible\CatalogProductCustomerBenefit\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $customerBenefitTableName = $setup->getTable('catalog_product_customer_benefit');

        if (version_compare(
            $context->getVersion(),
            '1.1.0',
            '<'
        )) {
            $connection = $setup->getConnection();

            if (! $connection->tableColumnExists(
                $customerBenefitTableName,
                'website_id'
            )) {
                $connection->addColumn(
                    $customerBenefitTableName,
                    'website_id',
                    [
                        'type'     => Table::TYPE_SMALLINT,
                        'length'   => 5,
                        'nullable' => false,
                        'unsigned' => true,
                        'default'  => 0,
                        'comment'  => 'Website ID',
                        'after'    => 'priority'
                    ]
                );

                $websiteTableName = $connection->getTableName('store_website');

                $connection->addForeignKey(
                    $connection->getForeignKeyName(
                        $customerBenefitTableName,
                        'website_id',
                        $websiteTableName,
                        'website_id'
                    ),
                    $customerBenefitTableName,
                    'website_id',
                    $websiteTableName,
                    'website_id'
                );
            }
        }

        $setup->endSetup();
    }
}