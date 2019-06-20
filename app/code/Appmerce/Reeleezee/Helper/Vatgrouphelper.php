<?php
/**
 * Copyright © 2019 Appmerce - Applications for Ecommerce
 * http://www.appmerce.com
 */
namespace Appmerce\Reeleezee\Helper;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Store\Model\Store;

class Vatgrouphelper extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Core store config
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig; 
    
    /**
     * @var \Magento\Framework\Math\Random
     */
    protected $mathRandom;
    
    /**
     * @var Json
     */
    private $serializer;
    
    /**
     * @var array
     */
    private $cache = [];
    
    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Math\Random $mathRandom
     * @param Json|null $serializer
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Math\Random $mathRandom,
        Json $serializer = null
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->mathRandom = $mathRandom;
        $this->serializer = $serializer ?: ObjectManager::getInstance()->get(Json::class);
    }
    
    /**
     * Get store configuration
     */
    public function getConfigData($path, $storeId = null)
    {
        return $this->scopeConfig->getValue($path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }
    
    /**
     * Generate a storable representation of a value
     *
     * @param int|float|string|array $value
     * @return string
     */
    protected function serializeValue($value)
    {
        if (is_array($value)) {
            $data = [];
            foreach ($value as $taxClassId => $vatGroup) {
                if (!array_key_exists($taxClassId, $data)) {
                    $data[$taxClassId] = $vatGroup;
                }
            }
            if (count($data) == 1 && array_key_exists('default', $data)) {
                return (string) $data['default'];
            }
            return $this->serializer->serialize($data);
        } else {
            return '';
        }
    }

    /**
     * Create a value from a storable representation
     *
     * @param int|float|string $value
     * @return array
     */
    protected function unserializeValue($value)
    {
        if (is_string($value) && !empty($value)) {
            return $this->serializer->unserialize($value);
        } else {
            return [];
        }
    }

    /**
     * Check whether value is in form retrieved by _encodeArrayFieldValue()
     *
     * @param string|array $value
     * @return bool
     */
    protected function isEncodedArrayFieldValue($value)
    {
        if (!is_array($value)) {
            return false;
        }
        unset($value['__empty']);
        foreach ($value as $resultId => $row) {
            if (!is_array($row)
                || !array_key_exists('tax_class_id', $row)
                || !array_key_exists('vat_group', $row)
            ) {
                return false;
            }
        }
        return true;
    }
    
    /**
     * Encode value to be used in \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray
     *
     * @param array $value
     * @return array
     */
    protected function encodeArrayFieldValue(array $value)
    {
        $result = [];
        foreach ($value as $taxClassId => $vatGroup) {
            $resultId = $this->mathRandom->getUniqueHash('_');
            $result[$resultId] = [
                'tax_class_id' => $taxClassId,
                'vat_group' => $vatGroup,
            ];
        }
        return $result;
    }

    /**
     * Decode value from used in \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray
     *
     * @param array $value
     * @return array
     */
    protected function decodeArrayFieldValue(array $value)
    {
        $result = [];
        unset($value['__empty']);
        foreach ($value as $resultId => $row) {
            if (!is_array($row)
                || !array_key_exists('tax_class_id', $row)
                || !array_key_exists('vat_group', $row)
            ) {
                continue;
            }
            $taxClassId = $row['tax_class_id'];
            $vatGroup = $row['vat_group'];
            $result[$taxClassId] = $vatGroup;
        }
        return $result;
    }

    /**
     * Retrieve min_sale_qty value from config
     *
     * @param int $customerGroupId
     * @param null|string|bool|int|Store $store
     * @return float|null
     */
    public function getConfigValue($productTaxClassId, $store = null)
    {
        $value = $this->getConfigData('reeleezee/taxes/vatgroup_mapping', $store);
        $value = $this->unserializeValue($value);
        if ($this->isEncodedArrayFieldValue($value)) {
            $value = $this->decodeArrayFieldValue($value);
        }
        $result = null;
        foreach ($value as $taxClassId => $vatGroup) {
            if ($taxClassId == $productTaxClassId) {
                $result = $vatGroup;
                break;
            }
        }
        return $result;
    }

    /**
     * Make value readable by \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray
     *
     * @param string|array $value
     * @return array
     */
    public function makeArrayFieldValue($value)
    {
        $value = $this->unserializeValue($value);
        if (!$this->isEncodedArrayFieldValue($value)) {
            $value = $this->encodeArrayFieldValue($value);
        }
        return $value;
    }

    /**
     * Make value ready for store
     *
     * @param string|array $value
     * @return string
     */
    public function makeStorableArrayFieldValue($value)
    {
        if ($this->isEncodedArrayFieldValue($value)) {
            $value = $this->decodeArrayFieldValue($value);
        }
        $value = $this->serializeValue($value);
        return $value;
    }
    
}
