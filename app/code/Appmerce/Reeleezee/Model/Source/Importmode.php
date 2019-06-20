<?php
/**
 * Copyright © 2019 Appmerce - Applications for Ecommerce
 * http://www.appmerce.com
 */
namespace Appmerce\Reeleezee\Model\Source;

class Importmode implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Datafield model type.
     * Data mapping.
     *
     * @return array
     */
    public function toOptionArray()
    {
        $dataType = [
            ['value' => 1, 'label' => __('Automatic and manual')],
            ['value' => 0, 'label' => __('Manual only')],
        ];

        return $dataType;
    }
}
