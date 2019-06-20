<?php
/**
 * Copyright © 2019 Appmerce - Applications for Ecommerce
 * http://www.appmerce.com
 */
namespace Appmerce\Reeleezee\Model\Source;

class Bookingmode implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Booking Modes
     */
    const BOOKING_DOCUMENT = 'document';
    const BOOKING_RECEIPT = 'receipt';
    
    /**
     * Possible values
     * 
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::BOOKING_DOCUMENT,
                'label' => __('Document')
            ],
            [
                'value' => self::BOOKING_RECEIPT,
                'label' => __('Receipt')
            ],
        ];
    }

}
