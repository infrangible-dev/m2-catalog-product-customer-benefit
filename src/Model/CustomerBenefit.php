<?php

declare(strict_types=1);

namespace Infrangible\CatalogProductCustomerBenefit\Model;

use Magento\Framework\Model\AbstractModel;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 *
 * @method string getSourceProductId()
 * @method void setSourceProductId(string $sourceProductId)
 * @method string getSourceProductOptionValueId()
 * @method void setSourceProductOptionValueId($sourceProductOptionValueId)
 * @method string getTargetProductId()
 * @method void setTargetProductId(string $targetProductId)
 * @method float|null getPrice()
 * @method void setPrice(float $price)
 * @method int|null getDiscount()
 * @method void setDiscount(int $discount)
 * @method int|null getLimit()
 * @method void setLimit(int $limit)
 * @method int|null getDaysAfterCreatedAt()
 * @method void setDaysAfterCreatedAt($daysAfterCreatedAt)
 * @method int getPriority()
 * @method void setPriority(int $priority)
 * @method int getActive()
 * @method void setActive(int $active)
 */
class CustomerBenefit extends AbstractModel
{
    protected $_eventPrefix = 'customer_benefit';

    protected function _construct(): void
    {
        $this->_init(ResourceModel\CustomerBenefit::class);
    }
}
