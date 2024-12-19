<?php

declare(strict_types=1);

namespace Infrangible\CatalogProductCustomerBenefit\Observer;

use FeWeDev\Base\Variables;
use Infrangible\CatalogProductCustomerBenefit\Model\Calculation\CustomerBenefitFactory;
use Infrangible\CatalogProductCustomerBenefit\Model\CustomerBenefit;
use Infrangible\CatalogProductCustomerBenefit\Model\ResourceModel\CustomerBenefit\CollectionFactory;
use Infrangible\CatalogProductPriceCalculation\Model\Calculations;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class CatalogProductPriceCalculation implements ObserverInterface
{
    /** @var CollectionFactory */
    protected $collectionFactory;

    /** @var CustomerBenefitFactory */
    protected $customerBenefitCalculationFactory;

    /** @var Variables */
    protected $variables;

    public function __construct(
        CollectionFactory $collectionFactory,
        CustomerBenefitFactory $customerBenefitCalculationFactory,
        Variables $variables
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->customerBenefitCalculationFactory = $customerBenefitCalculationFactory;
        $this->variables = $variables;
    }

    /**
     * @throws \Exception
     */
    public function execute(Observer $observer)
    {
        /** @var Calculations $calculations */
        $calculations = $observer->getData('calculations');

        $collection = $this->collectionFactory->create();
        $collection->addActiveFilter();

        /** @var CustomerBenefit $customerBenefit */
        foreach ($collection as $customerBenefit) {
            $customerBenefitCalculation = $this->customerBenefitCalculationFactory->create();

            $customerBenefitCalculation->setSourceProductId(
                $this->variables->intValue($customerBenefit->getSourceProductId())
            );
            if ($customerBenefit->getSourceProductOptionId()) {
                $customerBenefitCalculation->setSourceProductOptionId(
                    $this->variables->intValue($customerBenefit->getSourceProductOptionId())
                );
            }
            if ($customerBenefit->getSourceProductOptionValueId()) {
                $customerBenefitCalculation->setSourceProductOptionValueId(
                    $this->variables->intValue($customerBenefit->getSourceProductOptionValueId())
                );
            }
            $customerBenefitCalculation->setTargetProductId(
                $this->variables->intValue($customerBenefit->getTargetProductId())
            );
            if ($customerBenefit->getPrice()) {
                $customerBenefitCalculation->setPrice(floatval($customerBenefit->getPrice()));
            }
            if ($customerBenefit->getDiscount()) {
                $customerBenefitCalculation->setDiscount($this->variables->intValue($customerBenefit->getDiscount()));
            }
            if ($customerBenefit->getCreatedAtDaysBefore()) {
                $customerBenefitCalculation->setCreatedAtDaysBefore(
                    $this->variables->intValue($customerBenefit->getCreatedAtDaysBefore())
                );
            }
            if ($customerBenefit->getCustomerGroupIds()) {
                $customerBenefitCalculation->setCustomerGroupIds($customerBenefit->getCustomerGroupIds());
            }
            $customerBenefitCalculation->setPriority($this->variables->intValue($customerBenefit->getPriority()));
            $customerBenefitCalculation->setWebsiteId(
                $this->variables->intValue($customerBenefit->getWebsiteId())
            );

            $calculations->addCalculation($customerBenefitCalculation);
        }
    }
}
