<?php

declare(strict_types=1);

namespace Infrangible\CatalogProductCustomerBenefit\Observer;

use FeWeDev\Base\Arrays;
use FeWeDev\Base\Variables;
use Infrangible\CatalogProductCustomerBenefit\Helper\Data;
use Magento\Catalog\Model\Product\Option;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\ItemRepository;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class SalesOrderPlaceAfter implements ObserverInterface
{
    /** @var Variables */
    protected $variables;

    /** @var Arrays */
    protected $arrays;

    /** @var Data */
    protected $helper;

    /** @var ItemRepository */
    protected $itemRepository;

    public function __construct(
        Variables $variables,
        Arrays $arrays,
        Data $helper,
        ItemRepository $itemRepository
    ) {
        $this->variables = $variables;
        $this->arrays = $arrays;
        $this->helper = $helper;
        $this->itemRepository = $itemRepository;
    }

    /**
     * @throws \Exception
     */
    public function execute(Observer $observer): void
    {
        $order = $observer->getData('order');

        if ($order instanceof Order) {
            $customerId = $order->getCustomerId();

            if (! $customerId) {
                return;
            }

            foreach ($order->getItems() as $item) {
                $sourceProduct = $item->getProduct();

                $sourceProductId = $sourceProduct->getId();

                $sourceProductOptionIds = [];
                $sourceProductOptionValueIds = [];
                $customerBenefitOptionIds = [];

                $itemProductOptions = $item->getProductOptions();

                foreach ($this->arrays->getValue(
                    $itemProductOptions,
                    'options',
                    []
                ) as $itemProductOptionKey => $itemProductOption) {
                    $itemProductOptionId = $this->arrays->getValue(
                        $itemProductOption,
                        'option_id'
                    );

                    /** @var Option $productOption */
                    foreach ($sourceProduct->getProductOptionsCollection() as $productOption) {
                        if ($productOption->getId() == $itemProductOptionId) {
                            $productOptionPrice = $productOption->getPrice();

                            if ($item->getDiscountAmount()) {
                                $itemProductOptions[ 'options' ][ $itemProductOptionKey ][ 'original_price' ] =
                                    $productOptionPrice;

                                $productOptionDiscount = round(
                                    $productOptionPrice * $item->getDiscountAmount() / $item->getPrice(),
                                    2
                                );

                                $itemProductOptions[ 'options' ][ $itemProductOptionKey ][ 'discount' ] =
                                    $productOptionDiscount;

                                $productOptionPrice -= $productOptionDiscount;
                            }

                            $itemProductOptions[ 'options' ][ $itemProductOptionKey ][ 'price' ] = $productOptionPrice;
                        }
                    }

                    /** @var Option $sourceProductOption */
                    foreach ($sourceProduct->getProductOptionsCollection() as $sourceProductOption) {
                        if ($sourceProductOption->getId() == $itemProductOptionId) {
                            $sourceProductOptionValues = $sourceProductOption->getValues();

                            if ($sourceProductOptionValues === null) {
                                $sourceProductOptionIds[] = $itemProductOptionId;

                                if ($sourceProductOption->getType() === 'benefit_checkbox') {
                                    $customerBenefitOptionIds[] = $itemProductOptionId;
                                }
                            } else {
                                $sourceProductOptionValueId = $this->arrays->getValue(
                                    $itemProductOption,
                                    'option_value'
                                );

                                if ($sourceProductOptionValueId) {
                                    $sourceProductOptionValueIds[] = $sourceProductOptionValueId;
                                }
                            }
                        }
                    }
                }

                $targetProductPriceData = $this->helper->getTargetProductPriceData(
                    $this->variables->intValue($sourceProductId),
                    $sourceProductOptionIds,
                    $sourceProductOptionValueIds,
                    $customerBenefitOptionIds,
                    $this->variables->intValue($customerId)
                );

                if ($targetProductPriceData) {
                    $itemProductOptions[ 'customer_benefit_data' ] = $targetProductPriceData;

                    $item->setProductOptions($itemProductOptions);
                }
            }
        }
    }
}
