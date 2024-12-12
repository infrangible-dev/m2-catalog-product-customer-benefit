<?php

declare(strict_types=1);

namespace Infrangible\CatalogProductCustomerBenefit\Model\ResourceModel\CustomerBenefit;

use Infrangible\CatalogProductCustomerBenefit\Model\CustomerBenefit;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Collection extends AbstractCollection
{
    protected function _construct(): void
    {
        $this->_init(
            CustomerBenefit::class,
            \Infrangible\CatalogProductCustomerBenefit\Model\ResourceModel\CustomerBenefit::class
        );
    }

    public function addSourceProductFilter(int $productId)
    {
        $this->addFieldToFilter(
            'source_product_id',
            $productId
        );
    }

    public function addActiveFilter()
    {
        $this->getSelect()->where(
            'main_table.active = ?',
            1
        );
    }

    public function addPriorityOrder()
    {
        $this->getSelect()->order('main_table.priority DESC');
    }
}
