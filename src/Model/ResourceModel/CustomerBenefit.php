<?php

declare(strict_types=1);

namespace Infrangible\CatalogProductCustomerBenefit\Model\ResourceModel;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class CustomerBenefit extends AbstractDb
{
    protected function _construct(): void
    {
        $this->_init(
            'catalog_product_customer_benefit',
            'id'
        );
    }

    protected function _afterLoad(AbstractModel $object): CustomerBenefit
    {
        parent::_afterLoad($object);

        $customerGroupIds = $object->getData('customer_group_ids');

        if ($customerGroupIds && ! is_array($customerGroupIds)) {
            $object->setData(
                'customer_group_ids',
                explode(
                    ',',
                    $customerGroupIds
                )
            );
        } else {
            $object->setData(
                'customer_group_ids',
                []
            );
        }

        return $this;
    }

    /**
     * @throws \Exception
     */
    protected function _beforeSave(AbstractModel $object): CustomerBenefit
    {
        parent::_beforeSave($object);

        if ($object->getData('price') == '' && $object->getData('discount') == '') {
            throw new \Exception('Either price or discount must be set');
        }

        if ($object->getData('price') != '' && $object->getData('discount') != '') {
            throw new \Exception('Either price or discount must be set');
        }

        if (! $object->getId()) {
            $object->setData(
                'created_at',
                gmdate('Y-m-d H:i:s')
            );
        }

        $object->setData(
            'updated_at',
            gmdate('Y-m-d H:i:s')
        );

        if ($object->getData('source_product_option_value_id') == 0 ||
            $object->getData('source_product_option_value_id') == '') {
            $object->setData('source_product_option_value_id');
        }

        if ($object->getData('discount') == 0 || $object->getData('discount') == '') {
            $object->setData('discount');
        }

        if ($object->getData('price') == '' && $object->getData('discount') > 0) {
            $object->setData('price');
        }

        if ($object->getData('limit') == 0 || $object->getData('limit') == '') {
            $object->setData('limit');
        }

        if ($object->getData('created_at_days_before') == 0 || $object->getData('created_at_days_before') == '') {
            $object->setData('created_at_days_before');
        }

        $customerGroupIds = $object->getData('customer_group_ids');

        if (is_array($customerGroupIds)) {
            if (count($customerGroupIds) > 0) {
                $object->setData(
                    'customer_group_ids',
                    implode(
                        ',',
                        $customerGroupIds
                    )
                );
            } else {
                $object->setData('customer_group_ids');
            }
        } elseif ($object->getData('customer_group_ids') == '') {
            $object->setData('customer_group_ids');
        }

        if ($object->getData('api_flag') == '') {
            $object->setData('api_flag');
        }

        return $this;
    }
}
