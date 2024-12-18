<?php

declare(strict_types=1);

namespace Infrangible\CatalogProductCustomerBenefit\Block\Product\View\Options\Type;

use Magento\Catalog\Block\Product\View\Options\AbstractOptions;
use Magento\Catalog\Model\Product\Option;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class CustomerBenefit extends AbstractOptions
{
    public function getPreconfiguredValue(Option $option)
    {
        return $this->getProduct()->getPreconfiguredValues()->getData('options/' . $option->getId());
    }

    public function getCurrencyByStore(float $price)
    {
        return $this->pricingHelper->currencyByStore(
            $price,
            $this->getProduct()->getStore(),
            false
        );
    }

    public function formatPrice(float $price): string
    {
        return parent::_formatPrice(
            [
                'is_percent'    => false,
                'pricing_value' => $price
            ]
        );
    }
}
