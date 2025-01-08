<?php

declare(strict_types=1);

namespace Infrangible\CatalogProductCustomerBenefit\Controller\Adminhtml\Tab;

use Infrangible\CatalogProductCustomerBenefit\Traits\CustomerBenefit;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Add extends \Infrangible\BackendWidget\Controller\Backend\Object\Tab\Add
{
    use CustomerBenefit;
}
