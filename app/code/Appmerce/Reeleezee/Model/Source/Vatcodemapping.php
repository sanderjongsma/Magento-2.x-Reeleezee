<?php
/**
 * Copyright © 2019 Appmerce - Applications for Ecommerce
 * http://www.appmerce.com
 */
namespace Appmerce\Reeleezee\Model\Source;

use Magento\Framework\App\ObjectManager;
use Appmerce\Reeleezee\Helper\Vatcodehelper;

class Vatcodemapping extends \Magento\Framework\App\Config\Value
{       
    /**
     * Process data after load
     */
    public function afterLoad()
    {
        $value = $this->getValue();
        $helper = ObjectManager::getInstance()->get(Vatcodehelper::class);
        $value = $helper->makeArrayFieldValue($value);
        $this->setValue($value);
        return $this;
    }

    /**
     * Prepare data before save
     */
    public function beforeSave()
    {
        $value = $this->getValue();
        $helper = ObjectManager::getInstance()->get(Vatcodehelper::class);
        $value = $helper->makeStorableArrayFieldValue($value);
        $this->setValue($value);
        return $this;
    }
    
}
