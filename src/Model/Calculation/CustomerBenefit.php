<?php

declare(strict_types=1);

namespace Infrangible\CatalogProductCustomerBenefit\Model\Calculation;

use FeWeDev\Base\Json;
use FeWeDev\Base\Variables;
use Infrangible\CatalogProductPriceCalculation\Helper\Data;
use Infrangible\CatalogProductPriceCalculation\Model\Calculation\Base;
use Infrangible\CatalogProductPriceCalculation\Model\Calculation\Prices\SimpleFactory;
use Infrangible\CatalogProductPriceCalculation\Model\Calculation\PricesInterface;
use Infrangible\CatalogProductPriceCalculation\Model\CalculationDataInterface;
use Infrangible\Core\Helper\Customer;
use Infrangible\Core\Helper\Stores;
use Magento\Catalog\Model\Product;
use Magento\Checkout\Model\Session;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Pricing\Amount\AmountFactory;
use Magento\Quote\Model\Quote\Item;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class CustomerBenefit extends Base implements CalculationDataInterface
{
    /** @var Session */
    protected $checkoutSession;

    /** @var Data */
    protected $helper;

    /** @var Variables */
    protected $variables;

    /** @var Json */
    protected $json;

    /** @var Customer */
    protected $customerHelper;

    /** @var Stores */
    protected $storeHelper;

    /** @var int */
    private $sourceProductId;

    /** @var int|null */
    private $sourceProductOptionId;

    /** @var int|null */
    private $sourceProductOptionValueId;

    /** @var int */
    private $targetProductId;

    /** @var float|null */
    private $price;

    /** @var int|null */
    private $discount;

    /** @var int|null */
    private $createdAtDaysBefore;

    /** @var int */
    private $priority;

    /** @var int */
    private $websiteId;

    public function __construct(
        SimpleFactory $pricesFactory,
        AmountFactory $amountFactory,
        Session $checkoutSession,
        Data $helper,
        Variables $variables,
        Json $json,
        Customer $customerHelper,
        Stores $storeHelper
    ) {
        parent::__construct(
            $pricesFactory,
            $amountFactory
        );

        $this->checkoutSession = $checkoutSession;
        $this->helper = $helper;
        $this->variables = $variables;
        $this->json = $json;
        $this->customerHelper = $customerHelper;
        $this->storeHelper = $storeHelper;
    }

    public function getCode(): string
    {
        return sprintf(
            'cb_%s',
            md5(
                $this->json->encode(
                    [
                        'source_product_id' => $this->getSourceProductId(),
                        'target_product_id' => $this->getTargetProductId(),
                        'price'             => $this->getPrice(),
                        'discount'          => $this->getDiscount(),
                        'website_id'        => $this->getWebsiteId()
                    ]
                )
            )
        );
    }

    public function getSourceProductId(): int
    {
        return $this->sourceProductId;
    }

    public function setSourceProductId(int $sourceProductId): void
    {
        $this->sourceProductId = $sourceProductId;
    }

    public function getSourceProductOptionId(): ?int
    {
        return $this->sourceProductOptionId;
    }

    public function setSourceProductOptionId(?int $sourceProductOptionId): void
    {
        $this->sourceProductOptionId = $sourceProductOptionId;
    }

    public function getSourceProductOptionValueId(): ?int
    {
        return $this->sourceProductOptionValueId;
    }

    public function setSourceProductOptionValueId(?int $sourceProductOptionValueId): void
    {
        $this->sourceProductOptionValueId = $sourceProductOptionValueId;
    }

    public function getTargetProductId(): int
    {
        return $this->targetProductId;
    }

    public function setTargetProductId(int $targetProductId): void
    {
        $this->targetProductId = $targetProductId;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(?float $price): void
    {
        $this->price = $price;
    }

    public function getDiscount(): ?int
    {
        return $this->discount;
    }

    public function setDiscount(?int $discount): void
    {
        $this->discount = $discount;
    }

    public function getCreatedAtDaysBefore(): ?int
    {
        return $this->createdAtDaysBefore;
    }

    public function setCreatedAtDaysBefore(?int $createdAtDaysBefore): void
    {
        $this->createdAtDaysBefore = $createdAtDaysBefore;
    }

    public function getQuoteItemOptionCode(): string
    {
        return $this->getCode();
    }

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function setPriority(int $priority): void
    {
        $this->priority = $priority;
    }

    public function getWebsiteId(): int
    {
        return $this->websiteId;
    }

    public function setWebsiteId(int $websiteId): void
    {
        $this->websiteId = $websiteId;
    }

    public function hasProductCalculation(Product $product): bool
    {
        return $product->getId() == $this->getTargetProductId();
    }

    /**
     * @throws \Exception
     */
    public function getProductPrices(Product $product): PricesInterface
    {
        return $this->helper->calculatePrices(
            $product,
            $this
        );
    }

    /**
     * @throws NoSuchEntityException
     * @throws LocalizedException
     * @throws \Exception
     */
    public function isActive(): bool
    {
        $quote = $this->checkoutSession->getQuote();

        $customerId = $quote->getCustomerId();

        if (! $customerId) {
            return false;
        }

        if ($this->getWebsiteId() != 0) {
            $website = $this->storeHelper->getWebsite();
            $websiteId = $website->getId();

            if ($websiteId != $this->getWebsiteId()) {
                return false;
            }
        }

        $currentTimestamp = (new \DateTime())->getTimestamp();

        $customer = $this->customerHelper->loadCustomer($this->variables->intValue($customerId));
        $customerCreatedAtTimestamp = $customer->getCreatedAtTimestamp();

        $items = $quote->getItemsCollection();

        /** @var Item $item */
        foreach ($items as $item) {
            $productId = $this->variables->intValue($item->getProduct()->getId());

            if ($productId === $this->getTargetProductId()) {
                continue;
            }

            if ($productId !== $this->getSourceProductId()) {
                continue;
            }

            $sourceProductOptionId = $this->getSourceProductOptionId();

            if ($sourceProductOptionId) {
                $optionIdsOption = $item->getOptionByCode('option_ids');

                $optionIds = $optionIdsOption ? explode(
                    ',',
                    $optionIdsOption->getValue()
                ) : [];

                if (! in_array(
                    $sourceProductOptionId,
                    $optionIds
                )) {
                    continue;
                }
            }

            $sourceProductOptionValueId = $this->getSourceProductOptionValueId();

            if ($sourceProductOptionValueId) {
                $optionValueIds = [];

                $optionIdsOption = $item->getOptionByCode('option_ids');

                if ($optionIdsOption) {
                    $optionIds = explode(
                        ',',
                        $optionIdsOption->getValue()
                    );

                    foreach ($optionIds as $optionId) {
                        $optionIdOption = $item->getOptionByCode(
                            sprintf(
                                'option_%d',
                                $optionId
                            )
                        );

                        if ($optionIdOption) {
                            $optionValueIds = array_merge(
                                $optionValueIds,
                                explode(
                                    ',',
                                    $optionIdOption->getValue()
                                )
                            );
                        }
                    }
                }

                $optionValueIds = array_unique($optionValueIds);

                $optionValueIds = array_map([$this->variables, 'intValue'],
                    $optionValueIds);

                if (! in_array(
                    $sourceProductOptionValueId,
                    $optionValueIds
                )) {
                    continue;
                }
            }

            $daysAfterCreatedAt = $this->getCreatedAtDaysBefore();

            if ($daysAfterCreatedAt) {
                $checkTimestamp = $customerCreatedAtTimestamp + $daysAfterCreatedAt * 24 * 60 * 60;

                if ($currentTimestamp > $checkTimestamp) {
                    continue;
                }
            }

            return true;
        }

        return false;
    }
}
