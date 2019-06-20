<?php
/**
 * Copyright © 2019 Appmerce - Applications for Ecommerce
 * http://www.appmerce.com
 */
namespace Appmerce\Reeleezee\Block\Adminhtml\Form;

use Appmerce\Reeleezee\Model\Source\Vatgroup;

/**
 * HTML select element block with vat group options
 */
class Vatgroups extends \Magento\Framework\View\Element\Html\Select
{
    /**
     * Vat groups cache
     *
     * @var array
     */
    private $_vatGroups;

    /**
     * Flag whether to add group all option or no
     *
     * @var bool
     */
    protected $_addVatGroupAllOption = true;

    /**
     * Retrieve allowed vat groups
     *
     * @param int $classId  return name by customer group id
     * @return array|string
     */
    protected function _getVatGroups($classId = null)
    {
        if ($this->_vatGroups === null) {
            $vatGroup = new Vatgroup;
            $this->_vatGroups = $vatGroup->toOptionArray();
        }
        if (!is_null($classId)) {
            return isset($this->_vatGroups[$classId]) ? $this->_vatGroups[$classId] : null;
        }
        return $this->_vatGroups;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setInputName($value)
    {
        return $this->setName($value);
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    public function _toHtml()
    {
        if (!$this->getOptions()) {
            if ($this->_addVatGroupAllOption) {
                $this->addOption(
                    'default',
                    __('-- Choose --')
                );
            }
            foreach ($this->_getVatGroups() as $vatGroup) {
                $this->addOption($vatGroup['value'], addslashes($vatGroup['label']));
            }
        }
        return parent::_toHtml();
    }

}
