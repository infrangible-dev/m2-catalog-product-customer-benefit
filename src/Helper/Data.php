<?php

declare(strict_types=1);

namespace Infrangible\CatalogProductCustomerBenefit\Helper;

use FeWeDev\Base\Variables;
use Infrangible\CatalogProductCustomerBenefit\Model\CustomerBenefit;
use Infrangible\CatalogProductCustomerBenefit\Model\ResourceModel\CustomerBenefit\CollectionFactory;
use Infrangible\CatalogProductCustomerPrice\Helper\Cache;
use Infrangible\Core\Helper\Attribute;
use Infrangible\Core\Helper\Customer;
use Infrangible\Core\Helper\Database;
use Infrangible\Core\Helper\Stores;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Data extends AbstractHelper
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

    /** @var CollectionFactory */
    protected $customerBenefitCollectionFactory;

    /** @var Session */
    protected $customerSession;

    /** @var Attribute */
    protected $attributeHelper;

    /** @var Database */
    protected $databaseHelper;

    public function __construct(
        Context $context,
        CollectionFactory $collectionFactory,
        Customer $customerHelper,
        Variables $variables,
        Cache $cacheHelper,
        Stores $storeHelper,
        CollectionFactory $customerBenefitCollectionFactory,
        Session $customerSession,
        Attribute $attributeHelper,
        Database $databaseHelper
    ) {
        parent::__construct($context);

        $this->collectionFactory = $collectionFactory;
        $this->customerHelper = $customerHelper;
        $this->variables = $variables;
        $this->cacheHelper = $cacheHelper;
        $this->storeHelper = $storeHelper;
        $this->customerBenefitCollectionFactory = $customerBenefitCollectionFactory;
        $this->customerSession = $customerSession;
        $this->attributeHelper = $attributeHelper;
        $this->databaseHelper = $databaseHelper;
    }

    /**
     * @throws \Exception
     */
    public function getTargetProductPriceData(
        int $sourceProductId,
        array $sourceProductOptionIds,
        array $sourceProductOptionValueIds,
        array $customerBenefitOptionIds,
        int $customerId
    ): array {
        $website = $this->storeHelper->getWebsite();
        $websiteId = $website->getId();

        $currentTimestamp = (new \DateTime())->getTimestamp();

        $customer = $this->customerHelper->loadCustomer($this->variables->intValue($customerId));
        $customerCheckTimestamp = $this->getCustomerCheckTimestamp($customer);

        $collection = $this->collectionFactory->create();
        $collection->addSourceProductFilter($this->variables->intValue($sourceProductId));
        $collection->addActiveFilter();
        $collection->addPriorityOrder();
        $collection->addWebsiteFilter($this->variables->intValue($websiteId));
        $collection->addCustomerGroupFilter($this->variables->intValue($customer->getGroupId()));

        $isPaid = false;

        /** @var CustomerBenefit $customerBenefit */
        foreach ($collection as $customerBenefit) {
            $sourceProductOptionId = $customerBenefit->getSourceProductOptionId();

            if ($sourceProductOptionId) {
                $hasProductOption = false;

                foreach ($sourceProductOptionIds as $optionId) {
                    if ($optionId == $sourceProductOptionId) {
                        $hasProductOption = true;
                    }
                }

                if (! $hasProductOption) {
                    continue;
                }

                foreach ($customerBenefitOptionIds as $optionId) {
                    if ($optionId == $sourceProductOptionId) {
                        $isPaid = true;
                    }
                }
            }

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

            $createdAtDaysBefore = $customerBenefit->getCreatedAtDaysBefore();

            if ($createdAtDaysBefore) {
                $checkTimestamp = $customerCheckTimestamp + $createdAtDaysBefore * 24 * 60 * 60;

                if ($currentTimestamp > $checkTimestamp) {
                    continue;
                }
            }

            return [
                'customer_benefit_id' => $customerBenefit->getId(),
                'target_product_id'   => $customerBenefit->getTargetProductId(),
                'price'               => $isPaid ? 0 : $customerBenefit->getPrice(),
                'discount'            => $isPaid ? null : $customerBenefit->getDiscount(),
                'limit'               => $customerBenefit->getLimit(),
                'priority'            => $customerBenefit->getPriority(),
                'api_flag'            => $customerBenefit->getApiFlag()
            ];
        }

        return [];
    }

    /**
     * @throws \Exception
     */
    public function cleanProductCache(): void
    {
        $this->cacheHelper->cleanProductCache(
            'customer_benefit',
            'catalog_product_customer_benefit',
            'target_product_id',
            ['price', 'discount', 'priority', 'website_id']
        );
    }

    /**
     * @return CustomerBenefit[]
     *
     * @throws \Exception
     */
    public function getSourceProductOptionCustomerBenefits(
        int $sourceProductId,
        int $sourceProductOptionId,
        bool $checkCustomerCreatedAtDaysBefore
    ): array {
        $customerBenefitCollection = $this->customerBenefitCollectionFactory->create();

        $customerBenefitCollection->addSourceProductFilter($sourceProductId);
        $customerBenefitCollection->addSourceProductOptionFilter($sourceProductOptionId);
        $customerBenefitCollection->addWebsiteFilter(
            $this->variables->intValue($this->storeHelper->getWebsite()->getId())
        );
        $customerBenefitCollection->addActiveFilter();
        $customerBenefitCollection->addPriorityOrder();

        $customerBenefits = [];

        /** @var CustomerBenefit $customerBenefit */
        foreach ($customerBenefitCollection as $customerBenefit) {
            if (! $checkCustomerCreatedAtDaysBefore || $customerBenefit->checkCreatedAtDaysBefore()) {
                $customerBenefits[] = $customerBenefit;
            }
        }

        return $customerBenefits;
    }

    /**
     * @throws \Exception
     */
    public function getCheckTimestamp(): ?int
    {
        $customerId = $this->customerSession->getCustomerId();

        if (! $customerId) {
            return null;
        }

        $customerId = $this->variables->intValue($customerId);

        return $this->getCustomerIdCheckTimestamp($customerId);
    }

    public function getCustomerIdCheckTimestamp(int $customerId): int
    {
        $customer = $this->customerHelper->loadCustomer($customerId);

        return $this->getCustomerCheckTimestamp($customer);
    }

    public function getCustomerCheckTimestamp(\Magento\Customer\Model\Customer $customer): int
    {
        $customerCheckTimestamp = $customer->getCreatedAtTimestamp();

        $additionalAttributeIds =
            $this->storeHelper->getExplodedConfigValues('infrangible_catalogproductcustomerbenefit/customer/check');

        foreach ($additionalAttributeIds as $additionalAttributeId) {
            foreach ([$customer->getStoreId(), 0] as $storeId) {
                try {
                    $attributeValue = $this->attributeHelper->getAttributeValue(
                        $this->databaseHelper->getDefaultConnection(),
                        \Magento\Customer\Model\Customer::ENTITY,
                        $this->variables->stringValue($additionalAttributeId),
                        $this->variables->intValue($customer->getId()),
                        $this->variables->intValue($storeId)
                    );

                    if ($attributeValue) {
                        $attributeValueDatetime = \DateTime::createFromFormat(
                            'Y-m-d H:i:s',
                            $attributeValue
                        );

                        if ($attributeValueDatetime) {
                            $attributeValueTimestamp = $attributeValueDatetime->getTimestamp();

                            if ($attributeValueTimestamp > $customerCheckTimestamp) {
                                $customerCheckTimestamp = $attributeValueTimestamp;
                            }
                        }
                    }
                } catch (\Exception $exception) {
                }
            }
        }

        return $customerCheckTimestamp;
    }
}
