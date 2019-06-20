<?php
/**
 * Copyright © 2019 Appmerce - Applications for Ecommerce
 * http://www.appmerce.com
 */
namespace Appmerce\Reeleezee\Model\Source;

class Vatcode implements \Magento\Framework\Option\ArrayInterface
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
                'value' => 'H',
                'label' => __('NL, Hoog Tarief')
            ],
            [
                'value' => 'A',
                'label' => __('NL, Auto tarief')
            ],
            [
                'value' => 'L',
                'label' => __('NL, Laag tarief')
            ],
            [
                'value' => 'DX',
                'label' => __('EU + Ex-EU, Diensten Hoog Tarief (t/m 2009)')
            ],
            [
                'value' => 'IX',
                'label' => __('EU + Ex-EU, Diensten Laag tarief (t/m 2009)')
            ],
            [
                'value' => 'PE',
                'label' => __('EU, Producten Hoog tarief')
            ],
            [
                'value' => 'LE',
                'label' => __('EU, Producten Laag tarief')
            ],
            [
                'value' => 'PW',
                'label' => __('Ex Eu, Producten Hoog tarief')
            ],
            [
                'value' => 'LW',
                'label' => __('Ex EU, Producten Laag tarief')
            ],
            [
                'value' => 'VR',
                'label' => __('Geen BTW (Vrijgesteld)')
            ],
            [
                'value' => 'VH',
                'label' => __('NL, BTW verlegd (hoog)')
            ],
            [
                'value' => 'VL',
                'label' => __('NL, BTW verlegd (laag)')
            ],
            [
                'value' => 'N',
                'label' => __('NL, Nul tarief')
            ],
            [
                'value' => 'DE',
                'label' => __('Eu, Diensten Hoog tarief (vanaf 2010)')
            ],
            [
                'value' => 'IE',
                'label' => __('EU, Diensten Laag tarief (vanaf 2010)')
            ],
            [
                'value' => 'DW',
                'label' => __('Ex Eu, Diensten Hoog tarief (vanaf 2010)')
            ],
            [
                'value' => 'IW',
                'label' => __('Ex EU, Diensten Laag tarief (vanaf 2010)')
            ],
        ];
    }

}
