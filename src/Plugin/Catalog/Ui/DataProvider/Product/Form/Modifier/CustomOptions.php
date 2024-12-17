<?php

declare(strict_types=1);

namespace Infrangible\CatalogProductCustomerBenefit\Plugin\Catalog\Ui\DataProvider\Product\Form\Modifier;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class CustomOptions
{
    /**
     * @noinspection PhpUnusedParameterInspection
     */
    public function afterModifyMeta(
        \Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\CustomOptions $subject,
        array $meta
    ): array {
        $meta[ 'custom_options' ][ 'children' ][ \Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\CustomOptions::GRID_OPTIONS_NAME ][ 'children' ][ 'record' ][ 'children' ][ \Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\CustomOptions::CONTAINER_OPTION ][ 'children' ][ \Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\CustomOptions::CONTAINER_COMMON_NAME ][ 'children' ][ \Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\CustomOptions::FIELD_TYPE_NAME ][ 'arguments' ][ 'data' ][ 'config' ][ 'groupsConfig' ][ 'benefit' ] =
            [
                'values' => ['benefit_checkbox'],
                'indexes' => [
                    \Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\CustomOptions::CONTAINER_TYPE_STATIC_NAME,
                    \Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\CustomOptions::FIELD_SKU_NAME,
                ]
            ];

        return $meta;
    }
}
