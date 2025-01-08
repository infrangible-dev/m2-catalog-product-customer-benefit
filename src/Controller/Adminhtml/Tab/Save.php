<?php

declare(strict_types=1);

namespace Infrangible\CatalogProductCustomerBenefit\Controller\Adminhtml\Tab;

use Infrangible\CatalogProductCustomerBenefit\Traits\CustomerBenefit;
use Infrangible\CatalogProductCustomerBenefit\Traits\CustomerBenefitTab;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Save extends \Infrangible\BackendWidget\Controller\Backend\Object\Tab\Save
{
    use CustomerBenefit;
    use CustomerBenefitTab;

    protected function getObjectCreatedMessage(): string
    {
        return __('The customer benefit has been created.')->render();
    }

    protected function getObjectUpdatedMessage(): string
    {
        return __('The customer benefit has been saved.')->render();
    }
}
