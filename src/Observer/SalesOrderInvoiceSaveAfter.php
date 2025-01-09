<?php

declare(strict_types=1);

namespace Infrangible\CatalogProductCustomerBenefit\Observer;

use FeWeDev\Base\Arrays;
use FeWeDev\Base\Variables;
use Infrangible\CatalogProductCustomerPrice\Model\ProductCustomerPriceFactory;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Sales\Model\Order\Invoice;
use Magento\Sales\Model\Order\Item;
use Magento\Sales\Model\Order\ItemRepository;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class SalesOrderInvoiceSaveAfter implements ObserverInterface
{
    /** @var ProductCustomerPriceFactory */
    protected $customerPriceFactory;

    /** @var \Infrangible\CatalogProductCustomerPrice\Model\ResourceModel\ProductCustomerPriceFactory */
    protected $customerPriceResourceFactory;

    /** @var Variables */
    protected $variables;

    /** @var Arrays */
    protected $arrays;

    /** @var ItemRepository */
    protected $itemRepository;

    public function __construct(
        Variables $variables,
        Arrays $arrays,
        ProductCustomerPriceFactory $customerPriceFactory,
        \Infrangible\CatalogProductCustomerPrice\Model\ResourceModel\ProductCustomerPriceFactory $customerPriceResourceFactory,
        ItemRepository $itemRepository
    ) {
        $this->variables = $variables;
        $this->arrays = $arrays;
        $this->customerPriceFactory = $customerPriceFactory;
        $this->customerPriceResourceFactory = $customerPriceResourceFactory;
        $this->itemRepository = $itemRepository;
    }

    /**
     * @throws AlreadyExistsException
     * @throws \Exception
     */
    public function execute(Observer $observer): void
    {
        /** @var Invoice $invoice */
        $invoice = $observer->getData('invoice');

        $order = $invoice->getOrder();

        $customerId = $order->getCustomerId();

        if (! $customerId) {
            return;
        }

        $state = $invoice->getState();
        $isPaid = $state == Invoice::STATE_PAID;

        $originalState = $invoice->getOrigData('state');
        $wasPaid = $originalState == Invoice::STATE_PAID;

        if ($isPaid && ! $wasPaid) {
            $customerPriceResource = $this->customerPriceResourceFactory->create();

            $items = $order->getItems();

            /** @var Item $item */
            foreach ($items as $item) {
                $sourceProductOptions = $item->getProductOptions();

                $targetProductPriceData = $this->arrays->getValue(
                    $sourceProductOptions,
                    'customer_benefit_data'
                );

                if ($targetProductPriceData) {
                    $customerPrice = $this->customerPriceFactory->create();

                    $customerPrice->setCustomerId($this->variables->stringValue($customerId));
                    $customerPrice->setProductId(
                        $this->arrays->getValue(
                            $targetProductPriceData,
                            'target_product_id'
                        )
                    );
                    $customerPrice->setPrice(
                        $this->arrays->getValue(
                            $targetProductPriceData,
                            'price'
                        )
                    );
                    $customerPrice->setDiscount(
                        $this->arrays->getValue(
                            $targetProductPriceData,
                            'discount'
                        )
                    );
                    $customerPrice->setLimit(
                        $this->arrays->getValue(
                            $targetProductPriceData,
                            'limit'
                        )
                    );
                    $customerPrice->setPriority(
                        $this->arrays->getValue(
                            $targetProductPriceData,
                            'priority'
                        )
                    );
                    $customerPrice->setActive(1);

                    $customerPriceResource->save($customerPrice);

                    $sourceProductOptions[ 'customer_benefit_api_flag' ] = $this->arrays->getValue(
                        $targetProductPriceData,
                        'api_flag'
                    );

                    $item->setProductOptions($sourceProductOptions);

                    $this->itemRepository->save($item);
                }
            }
        }
    }
}
