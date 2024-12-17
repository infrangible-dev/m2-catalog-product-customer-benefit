<?php

declare(strict_types=1);

namespace Infrangible\CatalogProductCustomerBenefit\Model\Product\Option\Type;

use Magento\Catalog\Model\Product\Option\Type\DefaultType;
use Magento\Framework\Exception\LocalizedException;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class CustomerBenefit extends DefaultType
{
    /**
     * @throws LocalizedException
     */
    public function getFormattedOptionValue($optionValue): string
    {
        $option = $this->getOption();

        return $option->getTitle();
    }

    /**
     * @throws LocalizedException
     */
    public function getPrintableOptionValue($optionValue): string
    {
        $option = $this->getOption();

        return $option->getTitle();
    }
}