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

    protected function _afterLoad(): Collection
    {
        parent::_afterLoad();

        /** @var CustomerBenefit $item */
        foreach ($this->_items as $item) {
            $customerGroupIds = $item->getData('customer_group_ids');

            if ($customerGroupIds && ! is_array($customerGroupIds)) {
                $item->setData(
                    'customer_group_ids',
                    explode(
                        ',',
                        $customerGroupIds
                    )
                );
            } else {
                $item->setData(
                    'customer_group_ids',
                    []
                );
            }
        }

        return $this;
    }

    public function addSourceProductFilter(int $productId)
    {
        $this->addFieldToFilter(
            'source_product_id',
            $productId
        );
    }

    public function addSourceProductOptionFilter(int $optionId)
    {
        $this->addFieldToFilter(
            'source_product_option_id',
            $optionId
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

    public function addWebsiteFilter(int $websiteId, bool $includeAdmin = true)
    {
        if ($includeAdmin) {
            $this->getSelect()->where(
                'main_table.website_id IN (?)',
                [0, $websiteId]
            );
        } else {
            $this->getSelect()->where(
                'main_table.website_id = ?',
                $websiteId
            );
        }
    }

    public function addCustomerGroupFilter(int $customerGroupId)
    {
        $this->getSelect()->where(
            'FIND_IN_SET(?, main_table.customer_group_ids) > 0 OR main_table.customer_group_ids IS NULL',
            $customerGroupId
        );
    }
}
