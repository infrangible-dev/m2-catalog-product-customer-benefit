<?php

declare(strict_types=1);

namespace Infrangible\CatalogProductCustomerBenefit\Traits;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
trait CustomerBenefitTab
{
    protected function getParentObjectKey(): string
    {
        return 'source_product_id';
    }

    protected function getParentObjectValueKey(): string
    {
        return 'id';
    }
}
