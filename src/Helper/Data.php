<?php

declare(strict_types=1);

namespace Infrangible\CatalogProductCustomerBenefit\Helper;

use FeWeDev\Base\Variables;
use Infrangible\CatalogProductCustomerBenefit\Model\CustomerBenefit;
use Infrangible\CatalogProductCustomerBenefit\Model\ResourceModel\CustomerBenefit\CollectionFactory;
use Infrangible\CatalogProductCustomerPrice\Helper\Cache;
use Infrangible\Core\Helper\Customer;
use Infrangible\Core\Helper\Stores;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Data
{
    /** @var Variables */
    protected $variables;

    /** @var Customer */
    protected $customerHelper;

    /** @var CollectionFactory */
    protected $collectionFactory;

    /** @var Cache */
    protected $cacheHelper;

    /** @var Stores */
    protected $storeHelper;

    public function __construct(
        CollectionFactory $collectionFactory,
        Customer $customerHelper,
        Variables $variables,
        Cache $cacheHelper,
        Stores $storeHelper
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->customerHelper = $customerHelper;
        $this->variables = $variables;
        $this->cacheHelper = $cacheHelper;
        $this->storeHelper = $storeHelper;
    }

    /**
     * @throws \Exception
     */
    public function getTargetProductPriceData(
        int $sourceProductId,
        array $sourceProductOptionValueIds,
        int $customerId
    ): array {
        $website = $this->storeHelper->getWebsite();
        $websiteId = $website->getId();

        $currentTimestamp = (new \DateTime())->getTimestamp();

        $customer = $this->customerHelper->loadCustomer($this->variables->intValue($customerId));
        $customerCreatedAtTimestamp = $customer->getCreatedAtTimestamp();

        $collection = $this->collectionFactory->create();
        $collection->addSourceProductFilter($this->variables->intValue($sourceProductId));
        $collection->addActiveFilter();
        $collection->addPriorityOrder();
        $collection->addWebsiteFilter($this->variables->intValue($websiteId));

        /** @var CustomerBenefit $customerBenefit */
        foreach ($collection as $customerBenefit) {
            $sourceProductOptionValueId = $customerBenefit->getSourceProductOptionValueId();

            if ($sourceProductOptionValueId) {
                $hasProductOptionValue = false;

                foreach ($sourceProductOptionValueIds as $optionValueId) {
                    if ($optionValueId == $sourceProductOptionValueId) {
                        $hasProductOptionValue = true;
                    }
                }

                if (! $hasProductOptionValue) {
                    continue;
                }
            }

            $daysAfterCreatedAt = $customerBenefit->getDaysAfterCreatedAt();

            if ($daysAfterCreatedAt) {
                $checkTimestamp = $customerCreatedAtTimestamp + $daysAfterCreatedAt * 24 * 60 * 60;

                if ($currentTimestamp > $checkTimestamp) {
                    continue;
                }
            }

            return [
                'target_product_id' => $customerBenefit->getTargetProductId(),
                'price'             => $customerBenefit->getPrice(),
                'discount'          => $customerBenefit->getDiscount(),
                'limit'             => $customerBenefit->getLimit(),
                'priority'          => $customerBenefit->getPriority()
            ];
        }

        return [];
    }

    /**
     * @throws \Exception
     */
    public function cleanProductCache()
    {
        $this->cacheHelper->cleanProductCache(
            'customer_benefit',
            'catalog_product_customer_benefit',
            'target_product_id',
            ['price', 'discount', 'priority', 'website_id']
        );
    }
}
