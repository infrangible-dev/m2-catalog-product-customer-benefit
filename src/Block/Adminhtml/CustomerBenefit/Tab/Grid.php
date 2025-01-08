<?php

declare(strict_types=1);

namespace Infrangible\CatalogProductCustomerBenefit\Block\Adminhtml\CustomerBenefit\Tab;

use Infrangible\BackendWidget\Block\Grid\Tab;
use Magento\Framework\Data\Collection\AbstractDb;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Grid extends Tab
{
    protected function prepareCollection(AbstractDb $collection): void
    {
    }

    /**
     * @throws \Exception
     */
    protected function prepareFields(): void
    {
        $this->addProductNameColumn(
            'target_product_id',
            __('Target Product')->render()
        );

        $this->addNumberColumn(
            'price',
            __('Price')->render()
        );

        $this->addNumberColumn(
            'discount',
            __('Discount')->render()
        );

        $this->addNumberColumn(
            'limit',
            __('Limit')->render()
        );

        $this->addNumberColumn(
            'created_at_days_before',
            __('Days After Created At')->render()
        );

        $this->addCustomerGroupsColumn('customer_group_ids');

        $this->addNumberColumn(
            'priority',
            __('Priority')->render()
        );

        $this->addWebsiteNameColumn('website_id');

        $this->addYesNoColumn(
            'active',
            __('Active')->render()
        );
    }
}
