<?php /** @noinspection PhpDeprecationInspection */

declare(strict_types=1);

namespace Infrangible\CatalogProductCustomerBenefit\Model;

use FeWeDev\Base\Variables;
use Infrangible\Core\Helper\Customer;
use Infrangible\Core\Helper\Product;
use Infrangible\Core\Helper\Stores;
use Magento\Customer\Model\Session;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 *
 * @method string getSourceProductId()
 * @method void setSourceProductId(string $sourceProductId)
 * @method string getSourceProductOptionId()
 * @method void setSourceProductOptionId($sourceProductOptionId)
 * @method string getSourceProductOptionValueId()
 * @method void setSourceProductOptionValueId($sourceProductOptionValueId)
 * @method string getTargetProductId()
 * @method void setTargetProductId(string $targetProductId)
 * @method string|null getPrice()
 * @method void setPrice(string $price)
 * @method string|null getDiscount()
 * @method void setDiscount(string $discount)
 * @method string|null getLimit()
 * @method void setLimit(string $limit)
 * @method string|null getCreatedAtDaysBefore()
 * @method void setCreatedAtDaysBefore(string $createdAtDaysBefore)
 * @method int getPriority()
 * @method void setPriority(int $priority)
 * @method int getWebsiteId()
 * @method void setWebsiteId(int $websiteId)
 * @method int getActive()
 * @method void setActive(int $active)
 */
class CustomerBenefit extends AbstractModel
{
    protected $_eventPrefix = 'customer_benefit';

    /** @var Session */
    protected $customerSession;

    /** @var Customer */
    protected $customerHelper;

    /** @var Variables */
    protected $variables;

    /** @var Stores */
    protected $storeHelper;

    /** @var Product */
    protected $productHelper;

    public function __construct(
        Context $context,
        Registry $registry,
        Session $customerSession,
        Customer $customerHelper,
        Variables $variables,
        Stores $storeHelper,
        Product $productHelper,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection,
            $data
        );

        $this->customerSession = $customerSession;
        $this->customerHelper = $customerHelper;
        $this->variables = $variables;
        $this->storeHelper = $storeHelper;
        $this->productHelper = $productHelper;
    }

    protected function _construct(): void
    {
        $this->_init(ResourceModel\CustomerBenefit::class);
    }

    /**
     * @throws \Exception
     */
    public function getCustomerCreatedAtLimitDate(): ?\DateTime
    {
        $createdAtDaysBefore = $this->getCreatedAtDaysBefore();

        if (! $createdAtDaysBefore) {
            return null;
        }

        $customerId = $this->customerSession->getCustomerId();

        if (! $customerId) {
            return null;
        }

        $customerId = $this->variables->intValue($customerId);

        $customer = $this->customerHelper->loadCustomer($customerId);

        $createdAtTimestamp = $customer->getCreatedAtTimestamp();
        $customerDate = new \DateTime();
        $customerDate->setTimestamp($createdAtTimestamp);
        $customerDate->add(
            new \DateInterval(
                sprintf(
                    'P%dD',
                    $createdAtDaysBefore
                )
            )
        );

        return $customerDate;
    }

    /**
     * @throws \Exception
     */
    public function checkCreatedAtDaysBefore(): bool
    {
        $createdAtDaysBefore = $this->getCreatedAtDaysBefore();

        if (! $createdAtDaysBefore) {
            return true;
        }

        $customerCreatedAtLimitDate = $this->getCustomerCreatedAtLimitDate();

        if ($customerCreatedAtLimitDate === null) {
            return false;
        }

        return (new \DateTime())->getTimestamp() <= $customerCreatedAtLimitDate->getTimestamp();
    }

    /**
     * @throws \Exception
     */
    public function getTargetProductPrice(): float
    {
        $customerBenefitPrice = $this->getPrice();

        if ($customerBenefitPrice) {
            if (! is_float($customerBenefitPrice)) {
                $customerBenefitPrice = floatval($customerBenefitPrice);
            }

            return $customerBenefitPrice;
        } else {
            $customerBenefitDiscount = $this->getDiscount();

            $targetProduct = $this->productHelper->loadProduct(
                $this->variables->intValue($this->getTargetProductId()),
                $this->variables->intValue($this->storeHelper->getStore()->getId())
            );

            $targetProductFinalPrice = $targetProduct->getFinalPrice();

            return round(
                $targetProductFinalPrice * ((100 - $customerBenefitDiscount) / 100),
                2
            );
        }
    }
}
