<?php

declare(strict_types=1);

namespace Infrangible\CatalogProductCustomerBenefit\Plugin\Catalog\Block\Product\View;

use FeWeDev\Base\Arrays;
use FeWeDev\Base\Json;
use Magento\Catalog\Model\Product\Option;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Options
{
    /** @var Json */
    protected $json;

    /** @var Arrays */
    protected $arrays;

    public function __construct(
        Arrays $arrays,
        Json $json
    ) {
        $this->arrays = $arrays;
        $this->json = $json;
    }

    public function afterGetJsonConfig(\Magento\Catalog\Block\Product\View\Options $subject, string $config): string
    {
        $config = $this->json->decode($config);

        /** @var Option $option */
        foreach ($subject->getOptions() as $option) {
            if ($option->getType() === 'benefit_checkbox') {
                $optionId = $option->getId();

                $priceConfiguration = $this->arrays->getValue(
                    $config,
                    $optionId,
                    []
                );

                unset($config[ $optionId ]);

                $config[ $optionId ][ 1 ] = $priceConfiguration;
            }
        }

        return $this->json->encode($config);
    }
}
