<?php
/**
 * Copyright © 2019 Appmerce - Applications for Ecommerce
 * http://www.appmerce.com
 */
namespace Appmerce\Reeleezee\Model\Source;

class Attributes implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Possible values
     * 
     * @return array
     */
    public function toOptionArray()
    {
        $options = array();
        $attributes = $this->getAttributes();

        $options[] = array(
            'value' => '',
            'label' => __('Always use default')
        );
        foreach ($attributes as $value => $label) {
            $options[] = array(
                'value' => $value,
                'label' => $label
            );
        }

        return $options;
    }

}
