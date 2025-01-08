<?php

declare(strict_types=1);

namespace Infrangible\CatalogProductCustomerBenefit\Block\Adminhtml\Product;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Tab extends \Infrangible\BackendWidget\Block\Adminhtml\Product\Tab
{
    public function getTabUrl(): string
    {
        return $this->getUrl(
            'product_customer_benefit/tab',
            ['_current' => true]
        );
    }
}
