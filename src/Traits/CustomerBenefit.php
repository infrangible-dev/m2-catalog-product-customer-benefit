<?php

declare(strict_types=1);

namespace Infrangible\CatalogProductCustomerBenefit\Traits;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
trait CustomerBenefit
{
    protected function getModuleKey(): string
    {
        return 'Infrangible_CatalogProductCustomerBenefit';
    }

    protected function getResourceKey(): string
    {
        return 'infrangible_catalogproductcustomerbenefit';
    }

    protected function getMenuKey(): string
    {
        return 'infrangible_catalogproductcustomerbenefit_manage';
    }

    protected function getObjectName(): string
    {
        return 'CustomerBenefit';
    }

    protected function getObjectField(): string
    {
        return 'id';
    }

    protected function getTitle(): string
    {
        return __('Customer Benefit')->render();
    }

    protected function allowAdd(): bool
    {
        return true;
    }

    protected function allowEdit(): bool
    {
        return true;
    }

    protected function allowView(): bool
    {
        return false;
    }

    protected function allowDelete(): bool
    {
        return true;
    }

    protected function allowMassDelete(): bool
    {
        return true;
    }

    protected function getObjectNotFoundMessage(): string
    {
        return __('Unable to find the customer benefit with id: %d!')->render();
    }
}