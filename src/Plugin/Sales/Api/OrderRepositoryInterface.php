<?php

declare(strict_types=1);

namespace Infrangible\CatalogProductCustomerBenefit\Plugin\Sales\Api;

use FeWeDev\Base\Arrays;
use FeWeDev\Base\Variables;
use Magento\Catalog\Api\Data\CustomOptionExtension;
use Magento\Catalog\Api\Data\ProductOptionExtension;
use Magento\Catalog\Model\CustomOptions\CustomOption;
use Magento\Catalog\Model\Product\Option;
use Magento\Sales\Api\Data\OrderExtensionFactory;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderItemExtensionFactory;
use Magento\Sales\Api\Data\OrderSearchResultInterface;
use Magento\Sales\Model\Order\Item;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class OrderRepositoryInterface
{
    /** @var Arrays */
    protected $arrays;

    /** @var Variables */
    protected $variables;

    /** @var OrderExtensionFactory */
    protected $orderExtensionFactory;

    /** @var OrderItemExtensionFactory */
    protected $orderItemExtensionFactory;

    public function __construct(
        Arrays $arrays,
        Variables $variables,
        OrderExtensionFactory $orderExtensionFactory,
        OrderItemExtensionFactory $orderItemExtensionFactory
    ) {
        $this->arrays = $arrays;
        $this->variables = $variables;
        $this->orderExtensionFactory = $orderExtensionFactory;
        $this->orderItemExtensionFactory = $orderItemExtensionFactory;
    }

    /** @noinspection PhpUnusedParameterInspection */
    public function afterGetList(
        \Magento\Sales\Api\OrderRepositoryInterface $subject,
        OrderSearchResultInterface $orderSearchResult
    ): OrderSearchResultInterface {
        foreach ($orderSearchResult->getItems() as $order) {
            $this->addCustomerBenefitApiFlag($order);
        }

        return $orderSearchResult;
    }

    /** @noinspection PhpUnusedParameterInspection */
    public function afterGet(
        \Magento\Sales\Api\OrderRepositoryInterface $subject,
        OrderInterface $order
    ): OrderInterface {
        $this->addCustomerBenefitApiFlag($order);

        return $order;
    }

    private function addCustomerBenefitApiFlag(OrderInterface $order)
    {
        $orderApiFlags = [];

        foreach ($order->getItems() as $item) {
            if ($item instanceof Item) {
                $itemProductOptions = $item->getProductOptions();

                $apiFlag = $this->arrays->getValue(
                    $itemProductOptions,
                    'customer_benefit_api_flag'
                );

                if (! $this->variables->isEmpty($apiFlag)) {
                    $orderApiFlags[] = $apiFlag;

                    $extensionAttributes = $item->getExtensionAttributes();

                    $extensionAttributes = $extensionAttributes ? : $this->orderItemExtensionFactory->create();

                    $extensionAttributes->setCustomerBenefitApiFlag($apiFlag);

                    $item->setExtensionAttributes($extensionAttributes);
                }

                $itemProductOptionsOptions = $this->arrays->getValue(
                    $itemProductOptions,
                    'options',
                    []
                );

                foreach ($itemProductOptionsOptions as $itemProductOptionsOption) {
                    $itemProductOptionsOptionId = $this->arrays->getValue(
                        $itemProductOptionsOption,
                        'option_id'
                    );

                    /** @var Option $productOptionData */
                    $productOptionData = $item->getProductOption();

                    /** @var ProductOptionExtension $productOptionDataAttributes */
                    $productOptionDataAttributes = $productOptionData->getExtensionAttributes();

                    $customOptions = $productOptionDataAttributes->getCustomOptions();

                    /** @var CustomOption $customOption */
                    foreach ($customOptions as $customOption) {
                        if ($customOption->getOptionId() == $itemProductOptionsOptionId) {
                            /** @var CustomOptionExtension $customOptionExtensionAttributes */
                            $customOptionExtensionAttributes = $customOption->getExtensionAttributes();

                            $itemProductOptionsOptionOriginalPrice = $this->arrays->getValue(
                                $itemProductOptionsOption,
                                'original_price'
                            );
                            if ($itemProductOptionsOptionOriginalPrice !== null) {
                                $customOptionExtensionAttributes->setOriginalPrice(
                                    $itemProductOptionsOptionOriginalPrice
                                );
                            }

                            $itemProductOptionsOptionDiscount = $this->arrays->getValue(
                                $itemProductOptionsOption,
                                'discount'
                            );
                            if ($itemProductOptionsOptionDiscount !== null) {
                                $customOptionExtensionAttributes->setDiscount($itemProductOptionsOptionDiscount);
                            }

                            $itemProductOptionsOptionPrice = $this->arrays->getValue(
                                $itemProductOptionsOption,
                                'price'
                            );
                            if ($itemProductOptionsOptionPrice) {
                                $customOptionExtensionAttributes->setPrice($itemProductOptionsOptionPrice);
                            }

                            $customOption->setExtensionAttributes($customOptionExtensionAttributes);
                        }
                    }
                }
            }
        }

        if (! $this->variables->isEmpty($orderApiFlags)) {
            $orderApiFlags = array_unique($orderApiFlags);

            $extensionAttributes = $order->getExtensionAttributes();

            $extensionAttributes = $extensionAttributes ? : $this->orderExtensionFactory->create();

            $extensionAttributes->setCustomerBenefitApiFlag(
                implode(
                    ',',
                    $orderApiFlags
                )
            );

            foreach ($orderApiFlags as $orderApiFlag) {
                $extensionAttributes->setData(
                    $orderApiFlag,
                    true
                );
            }

            $order->setExtensionAttributes($extensionAttributes);
        }
    }
}
