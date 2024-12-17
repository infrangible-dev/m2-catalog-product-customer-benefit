<?php

declare(strict_types=1);

namespace Infrangible\CatalogProductCustomerBenefit\Block\Adminhtml\Product\Edit\Tab\Options\Type;

use Magento\Catalog\Block\Adminhtml\Product\Edit\Tab\Options\Type\AbstractType;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class CustomerBenefit extends AbstractType
{
    protected $_template = 'Infrangible_CatalogProductCustomerBenefit::catalog/product/edit/options/type/benefit.phtml';
}
