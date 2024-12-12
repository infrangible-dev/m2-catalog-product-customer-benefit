<?php

declare(strict_types=1);

namespace Infrangible\CatalogProductCustomerBenefit\Controller\Adminhtml\Index;

use Infrangible\CatalogProductCustomerBenefit\Traits\CustomerBenefit;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Delete extends \Infrangible\BackendWidget\Controller\Backend\Object\Delete
{
    use CustomerBenefit;

    protected function getObjectDeletedMessage(): string
    {
        return __('The customer price has been deleted.')->render();
    }
}
