<?php

declare(strict_types=1);

namespace Infrangible\CatalogProductCustomerBenefit\Plugin\Catalog\Model\Product;

use FeWeDev\Base\Variables;
use Infrangible\CatalogProductCustomerBenefit\Helper\Data;
use Infrangible\Core\Helper\Product;
use Infrangible\Core\Helper\Stores;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Option
{
    /** @var Variables */
    protected $variables;

    /** @var Stores */
    protected $storeHelper;

    /** @var Product */
    protected $productHelper;

    /** @var Data */
    protected $helper;

    public function __construct(
        Stores $storeHelper,
        Variables $variables,
        Product $productHelper,
        Data $helper
    ) {
        $this->storeHelper = $storeHelper;
        $this->variables = $variables;
        $this->productHelper = $productHelper;
        $this->helper = $helper;
    }

    /**
     * @throws \Exception
     */
    public function aroundGetPrice(
        \Magento\Catalog\Model\Product\Option $subject,
        callable $proceed,
        $flag = false
    ) {
        if ($subject->getType() === 'benefit_checkbox' && $subject->getProductId() && $subject->getOptionId()) {
            return $this->getPrice($subject);
        }

        return $proceed($flag);
    }

    /**
     * @throws \Exception
     */
    public function aroundGetRegularPrice(
        \Magento\Catalog\Model\Product\Option $subject,
        callable $proceed
    ) {
        if ($subject->getType() === 'benefit_checkbox' && $subject->getProductId() && $subject->getOptionId()) {
            return $this->getPrice($subject);
        }

        return $proceed();
    }

    /**
     * @throws \Exception
     */
    protected function getPrice(\Magento\Catalog\Model\Product\Option $option)
    {
        if (! $option->getData(\Magento\Catalog\Model\Product\Option::KEY_PRICE)) {
            $sourceProductId = $this->variables->intValue($option->getProductId());
            $sourceProductOptionId = $this->variables->intValue($option->getOptionId());

            $customerBenefits = $this->helper->getSourceProductOptionCustomerBenefits(
                $sourceProductId,
                $sourceProductOptionId,
                true
            );

            if (count($customerBenefits)) {
                $customerBenefit = reset($customerBenefits);

                $customerBenefitPrice = $customerBenefit->getPrice();

                if ($customerBenefitPrice) {
                    if (! is_float($customerBenefitPrice)) {
                        $customerBenefitPrice = floatval($customerBenefitPrice);
                    }

                    $option->setPrice($customerBenefitPrice);
                } else {
                    $customerBenefitDiscount = $customerBenefit->getDiscount();

                    $targetProduct = $this->productHelper->loadProduct(
                        $this->variables->intValue($customerBenefit->getTargetProductId()),
                        $this->variables->intValue($this->storeHelper->getStore()->getId())
                    );

                    $targetProductFinalPrice = $targetProduct->getFinalPrice();

                    $optionPrice = round(
                        $targetProductFinalPrice * ((100 - $customerBenefitDiscount) / 100),
                        2
                    );

                    $option->setPrice($optionPrice);
                }
            }
        }

        return $option->getData(\Magento\Catalog\Model\Product\Option::KEY_PRICE);
    }
}
