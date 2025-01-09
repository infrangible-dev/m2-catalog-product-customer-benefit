<?php

declare(strict_types=1);

namespace Infrangible\CatalogProductCustomerBenefit\Plugin\Framework\Reflection;

use Magento\Framework\Api\ExtensionAttributesInterface;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class ExtensionAttributesProcessor
{
    /** @noinspection PhpUnusedParameterInspection */
    public function afterBuildOutputDataArray(
        \Magento\Framework\Reflection\ExtensionAttributesProcessor $subject,
        array $result,
        ExtensionAttributesInterface $dataObject,
        string $dataObjectType
    ): array {
        if ($dataObjectType === '\Magento\Sales\Api\Data\OrderExtensionInterface' ||
            $dataObjectType === '\Magento\Sales\Api\Data\OrderItemExtensionInterface') {

            if (array_key_exists(
                'customer_benefit_api_flag',
                $result
            )) {
                $result[ $result[ 'customer_benefit_api_flag' ] ] = true;
            }
        }

        return $result;
    }
}
