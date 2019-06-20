<?php
/**
 * Copyright © 2019 Appmerce - Applications for Ecommerce
 * http://www.appmerce.com
 */
namespace Appmerce\Reeleezee\Model\Source;

use Magento\Framework\App\ObjectManager;
use Appmerce\Reeleezee\Helper\Vatgrouphelper;

class Vatgroupmapping extends \Magento\Framework\App\Config\Value
{
    /**
     * Process data after load
     */
    public function afterLoad()
    {
        $value = $this->getValue();
        $helper = ObjectManager::getInstance()->get(Vatgrouphelper::class);
        $value = $helper->makeArrayFieldValue($value);
        $this->setValue($value);
    }

    /**
     * Prepare data before save
     */
    public function beforeSave()
    {
        $value = $this->getValue();
        $helper = ObjectManager::getInstance()->get(Vatgrouphelper::class);
        $value = $helper->makeStorableArrayFieldValue($value);
        $this->setValue($value);
    }

}
