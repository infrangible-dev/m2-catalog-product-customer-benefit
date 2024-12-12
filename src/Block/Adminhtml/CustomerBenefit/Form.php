<?php

declare(strict_types=1);

namespace Infrangible\CatalogProductCustomerBenefit\Block\Adminhtml\CustomerBenefit;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Form extends \Infrangible\BackendWidget\Block\Form
{
    /**
     * @throws \Exception
     */
    protected function prepareFields(\Magento\Framework\Data\Form $form): void
    {
        $fieldSet = $form->addFieldset(
            'general',
            ['legend' => __('General')]
        );

        $this->addProductNameFieldWithProductOptions(
            $fieldSet,
            'source_product_id',
            __('Source Product')->render(),
            ['source_product_option_value_id'],
            true
        );

        $this->addProductOptionField(
            $fieldSet,
            'source_product_id',
            'source_product_option_value_id',
            __('Source Product Option Value')->render()
        );

        $this->addProductNameField(
            $fieldSet,
            'target_product_id',
            __('Target Product')->render(),
            true
        );

        $this->addPriceField(
            $fieldSet,
            'price',
            __('Price')->render()
        );

        $this->addDiscountField(
            $fieldSet,
            'discount',
            __('Discount')->render()
        );

        $this->addIntegerField(
            $fieldSet,
            'limit',
            __('Limit')->render()
        );

        $this->addIntegerField(
            $fieldSet,
            'days_after_created_at',
            __('Days After Created At')->render()
        );

        $this->addTextField(
            $fieldSet,
            'api_flag',
            __('API Flag')->render()
        );

        $this->addIntegerField(
            $fieldSet,
            'priority',
            __('Priority')->render()
        );

        $this->addYesNoWithDefaultField(
            $fieldSet,
            'active',
            __('Active')->render(),
            1
        );
    }
}
