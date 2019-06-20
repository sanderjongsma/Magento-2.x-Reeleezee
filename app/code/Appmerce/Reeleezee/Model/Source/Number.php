<?php
/**
 * Copyright © 2019 Appmerce - Applications for Ecommerce
 * http://www.appmerce.com
 */
namespace Appmerce\Reeleezee\Model\Source;

class Number implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Possible values
     * 
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => 'reeleezee',
                'label' => __('Reeleezee Invoice Number'),
            ],
            [
                'value' => 'invoice',
                'label' => __('Magento Invoice Number'),
            ],
            [
                'value' => 'order',
                'label' => __('Magento Order Number'),
            ],
        ];
    }

}
