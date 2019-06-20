<?php
/**
 * Copyright © 2019 Appmerce - Applications for Ecommerce
 * http://www.appmerce.com
 */
namespace Appmerce\Reeleezee\Model\Source;

class Unitcode implements \Magento\Framework\Option\ArrayInterface
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
                'value' => 'AC',
                'label' => __('Acre')
            ],
            [
                'value' => 'AG',
                'label' => __('Am.Gallon')
            ],
            [
                'value' => 'AR',
                'label' => __('Are')
            ],
            [
                'value' => 'ARR',
                'label' => __('Arroba')
            ],
            [
                'value' => 'BG',
                'label' => __('Br. Gallon')
            ],
            [
                'value' => 'BIT',
                'label' => __('Bit')
            ],
            [
                'value' => 'C',
                'label' => __('Graad Celcius')
            ],
            [
                'value' => 'CC',
                'label' => __('Kub centimeter')
            ],
            [
                'value' => 'CM',
                'label' => __('Centimeter')
            ],
            [
                'value' => 'CR',
                'label' => __('Crore')
            ],
            [
                'value' => 'CY',
                'label' => __('Candy')
            ],
            [
                'value' => 'DAN',
                'label' => __('Dan (Picul)')
            ],
            [
                'value' => 'DG',
                'label' => __('Dag')
            ],
            [
                'value' => 'DZ',
                'label' => __('Dozijn')
            ],
            [
                'value' => 'F',
                'label' => __('Graad Fahrenheit')
            ],
            [
                'value' => 'FT',
                'label' => __('Foot')
            ],
            [
                'value' => 'FT2',
                'label' => __('Sq foot')
            ],
            [
                'value' => 'FT3',
                'label' => __('Cubic foot')
            ],
            [
                'value' => 'GB',
                'label' => __('Gigabyte')
            ],
            [
                'value' => 'GR',
                'label' => __('Gram')
            ],
            [
                'value' => 'GS',
                'label' => __('Gros')
            ],
            [
                'value' => 'HA',
                'label' => __('Hectare')
            ],
            [
                'value' => 'HR',
                'label' => __('Uur')
            ],
            [
                'value' => 'IN',
                'label' => __('Inch')
            ],
            [
                'value' => 'IN2',
                'label' => __('Sq inch')
            ],
            [
                'value' => 'IN3',
                'label' => __('Cubic inch')
            ],
            [
                'value' => 'JIN',
                'label' => __('Jin (Catty)')
            ],
            [
                'value' => 'JR',
                'label' => __('Jaar')
            ],
            [
                'value' => 'K',
                'label' => __('Graad Kelvin')
            ],
            [
                'value' => 'KB',
                'label' => __('Kilobyte')
            ],
            [
                'value' => 'KG',
                'label' => __('Kilo')
            ],
            [
                'value' => 'KM',
                'label' => __('Kilometer')
            ],
            [
                'value' => 'KM2',
                'label' => __('Vk kilometer')
            ],
            [
                'value' => 'LB',
                'label' => __('Pounds')
            ],
            [
                'value' => 'LK',
                'label' => __('Lakh')
            ],
            [
                'value' => 'LT',
                'label' => __('Liter')
            ],
            [
                'value' => 'M',
                'label' => __('Meter')
            ],
            [
                'value' => 'M2',
                'label' => __('Meter ²')
            ],
            [
                'value' => 'M3',
                'label' => __('Meter 3')
            ],
            [
                'value' => 'MA',
                'label' => __('Manzana')
            ],
            [
                'value' => 'MB',
                'label' => __('Megabyte')
            ],
            [
                'value' => 'MD',
                'label' => __('Maand')
            ],
            [
                'value' => 'MG',
                'label' => __('Milligram')
            ],
            [
                'value' => 'MI',
                'label' => __('Mijl')
            ],
            [
                'value' => 'MI2',
                'label' => __('Sq mile')
            ],
            [
                'value' => 'MN',
                'label' => __('Minuut')
            ],
            [
                'value' => 'MS',
                'label' => __('Milliseconde')
            ],
            [
                'value' => 'MU',
                'label' => __('Mu')
            ],
            [
                'value' => 'NM',
                'label' => __('Nautische mijl')
            ],
            [
                'value' => 'OZ',
                'label' => __('Ounce')
            ],
            [
                'value' => 'OZT',
                'label' => __('Ounce troy')
            ],
            [
                'value' => 'PT',
                'label' => __('Pint')
            ],
            [
                'value' => 'QU',
                'label' => __('Quintal')
            ],
            [
                'value' => 'SC',
                'label' => __('Seconde')
            ],
            [
                'value' => 'ST',
                'label' => __('Stuk')
            ],
            [
                'value' => 'TN',
                'label' => __('Ton')
            ],
            [
                'value' => 'TNA',
                'label' => __('Tonelada')
            ],
            [
                'value' => 'TNL',
                'label' => __('Long ton')
            ],
            [
                'value' => 'TNS',
                'label' => __('Short ton')
            ],
            [
                'value' => 'VA',
                'label' => __('Vara')
            ],
            [
                'value' => 'WD',
                'label' => __('Werkdag')
            ],
            [
                'value' => 'WE',
                'label' => __('Weekend')
            ],
            [
                'value' => 'WH',
                'label' => __('Werkuur')
            ],
            [
                'value' => 'WK',
                'label' => __('Week')
            ],
            [
                'value' => 'WM',
                'label' => __('Werkmaand')
            ],
            [
                'value' => 'WW',
                'label' => __('Werkweek')
            ],
            [
                'value' => 'YD',
                'label' => __('Yard')
            ],
            [
                'value' => 'YD2',
                'label' => __('Sq yard')
            ],
            [
                'value' => 'ZM',
                'label' => __('Zeemijl')
            ],
        ];
    }

}
