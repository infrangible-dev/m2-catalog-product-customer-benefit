<?php

declare(strict_types=1);

namespace Infrangible\CatalogProductCustomerBenefit\Controller\Adminhtml\Index;

use Infrangible\CatalogProductCustomerBenefit\Traits\CustomerBenefit;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Index extends \Infrangible\BackendWidget\Controller\Backend\Object\Index
{
    use CustomerBenefit;
}
