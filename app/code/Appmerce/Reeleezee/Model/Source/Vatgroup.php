<?php
/**
 * Copyright © 2019 Appmerce - Applications for Ecommerce
 * http://www.appmerce.com
 */
namespace Appmerce\Reeleezee\Model\Source;

class Vatgroup implements \Magento\Framework\Option\ArrayInterface
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
                'value' => 'HD',
                'label' => __('Hoog tarief, Diensten')
            ],
            [
                'value' => 'HP',
                'label' => __('Hoog tarief, Produkten')
            ],
            [
                'value' => 'LD',
                'label' => __('Laag tarief, Diensten')
            ],
            [
                'value' => 'LP',
                'label' => __('Laag tarief, Produkten')
            ],
            [
                'value' => 'N',
                'label' => __('Nul tarief')
            ],
            [
                'value' => 'V',
                'label' => __('Vrijgesteld')
            ],
        ];
    }

}
